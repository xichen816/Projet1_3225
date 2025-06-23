<?php
class Review
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getOwnerId($reviewId)
    {
        $sql = 'SELECT id_utilisateur FROM revues WHERE id = :reviewId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_utilisateur'] : null;
    }

    public function fetchAll()
    {
        $sql = "SELECT r.*, u.nom AS username, c.nom AS cafename
                FROM revues r
                JOIN utilisateurs u ON r.id_utilisateur = u.id
                JOIN cafes c ON r.id_cafe = c.id
                ORDER BY r.date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $reviewIds = array_column($reviews, 'id');
        if ($reviewIds) {
            $inQuery = implode(',', array_fill(0, count($reviewIds), '?'));
            $stmtPhotos = $this->pdo->prepare(
                "SELECT id_revue, filepath FROM photos_revue WHERE id_revue IN ($inQuery)"
            );
            $stmtPhotos->execute($reviewIds);
            $photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);

            // Map photos to their reviews
            $photosByReview = [];
            foreach ($photos as $photo) {
                $photosByReview[$photo['id_revue']][] = $photo;
            }

            foreach ($reviews as &$review) {
                $review['photos'] = $photosByReview[$review['id']] ?? [];
            }
        }
        return $reviews;
    }

    public function fetchById($id)
    {
        $sql = "SELECT r.*, u.nom AS username, c.nom AS cafename
                FROM revues r
                JOIN utilisateurs u ON r.id_utilisateur = u.id
                JOIN cafes c ON r.id_cafe = c.id
                WHERE r.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchByUserId(int $userId)
    {
        $sql = "SELECT r.*, u.nom AS username, c.nom AS cafename
            FROM revues r
            JOIN utilisateurs u ON r.id_utilisateur = u.id
            JOIN cafes c ON r.id_cafe = c.id
            WHERE r.id_utilisateur = :userId
            ORDER BY r.date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $reviewIds = array_column($reviews, 'id');
        if ($reviewIds) {
            $inQuery = implode(',', array_fill(0, count($reviewIds), '?'));
            $stmtPhotos = $this->pdo->prepare(
                "SELECT id_revue, filepath FROM photos_revue WHERE id_revue IN ($inQuery)"
            );
            $stmtPhotos->execute($reviewIds);
            $photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);

            $photosByReview = [];
            foreach ($photos as $photo) {
                $photosByReview[$photo['id_revue']][] = $photo;
            }

            foreach ($reviews as &$review) {
                $review['photos'] = $photosByReview[$review['id']] ?? [];
            }
        }
        return $reviews;

    }

    public function fetchFeed($userId)
    {
        $sql = "
        SELECT r.id, r.titre, r.contenu, r.rating, r.date,
               u.id AS userid, u.nom AS username,
               c.id AS cafeid, c.nom AS cafename
        FROM revues r
        JOIN utilisateurs u ON r.id_utilisateur = u.id
        JOIN cafes c ON r.id_cafe = c.id
        WHERE r.id_utilisateur != :uid
        ORDER BY r.date DESC
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Attach photos as before
        $reviewIds = array_column($reviews, 'id');
        $photosByReview = [];
        if ($reviewIds) {
            $inQuery = implode(',', array_fill(0, count($reviewIds), '?'));
            $stmtPhotos = $this->pdo->prepare(
                "SELECT id_revue, filepath FROM photos_revue WHERE id_revue IN ($inQuery) AND is_primary = TRUE"
            );
            $stmtPhotos->execute($reviewIds);
            $photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);
            foreach ($photos as $photo) {
                $photosByReview[$photo['id_revue']][] = $photo;
            }
        }
        foreach ($reviews as &$review) {
            $review['thumbnail'] = $this->fetchThumbnailById($review['id']);
            $review['photos'] = $photosByReview[$review['id']] ?? [];
        }
        return $reviews;
    }


    public function fetchPhotosById($id)
    {
        $sql = "SELECT filepath FROM photos_revue WHERE id_revue = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchThumbnailById($id)
    {
        $sql = "SELECT filepath FROM photos_revue WHERE id_revue = :id AND is_primary = TRUE LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $photo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $photo ? $photo['filepath'] : null;
    }

    public function create($data)
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO revues (id_cafe, id_utilisateur, rating, contenu, description, titre)
                VALUES (:id_cafe, :id_utilisateur, :rating, :contenu, :description,  :titre)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_cafe' => $data['id_cafe'],
                ':id_utilisateur' => $data['id_utilisateur'],
                ':rating' => $data['rating'],
                ':contenu' => $data['contenu'],
                ':description' => ($data['description'] = mb_substr($data['contenu'], 0, 120) . '...') ?? null,
                ':titre' => $data['titre'],
            ]);
            $reviewId = $this->pdo->lastInsertId();

            // If there are photos, insert each into photos_revue
            if (!empty($data['photos'])) {
                foreach ($data['photos'] as $i => $photo) {
                    $stmtPhoto = $this->pdo->prepare(
                        "INSERT INTO photos_revue (id_revue, filepath, is_primary) VALUES (:id_revue, :filepath, :is_primary)"
                    );
                    $stmtPhoto->execute([
                        ':id_revue' => $reviewId,
                        ':filepath' => $photo['filepath'],
                        ':is_primary' => ($i === 0) ? 1 : 0,
                    ]);
                }
            }

            $this->pdo->commit();
            return $reviewId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }


    public function update($id, $data)
    {
        $sql = "UPDATE revues SET titre = :titre, contenu = :contenu, rating = :rating
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':contenu' => $data['contenu'],
            // ':description' => ($data['description'] = mb_substr($data['contenu'], 0, 120) . '...') ?? null,
            ':rating' => $data['rating'],
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM revues WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

}