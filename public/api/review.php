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
        $stmt->execute([':reviewId' => $reviewId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_utilisateur'] : null;
    }

    private function getPhotosByReviewIds(array $reviewIds, bool $onlyPrimary = false): array
    {
        if (empty($reviewIds))
            return [];

        $inQuery = implode(',', array_fill(0, count($reviewIds), '?'));
        $sql = "SELECT id_revue, filepath FROM photos_revue WHERE id_revue IN ($inQuery)";
        if ($onlyPrimary) {
            $sql .= " AND is_primary = TRUE";
        }

        $stmtPhotos = $this->pdo->prepare($sql);
        $stmtPhotos->execute($reviewIds);
        $photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);

        $photosByReview = [];
        foreach ($photos as $photo) {
            $photosByReview[$photo['id_revue']][] = $photo;
        }

        return $photosByReview;
    }

    private function getCategoriesByReviewIds(array $reviewIds): array
    {
        if (empty($reviewIds))
            return [];

        $inQuery = implode(',', array_fill(0, count($reviewIds), '?'));
        $sql = "
            SELECT rc.id_revue, cat.nom
            FROM revues_categories rc
            JOIN categories cat ON rc.id_categorie = cat.id
            WHERE rc.id_revue IN ($inQuery)
        ";

        $stmtCategories = $this->pdo->prepare($sql);
        $stmtCategories->execute($reviewIds);
        $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

        $categoriesByReview = [];
        foreach ($categories as $category) {
            $categoriesByReview[$category['id_revue']][] = $category['nom'];
        }

        return $categoriesByReview;
    }

    public function fetchAll()
    {
        $sql = "
        SELECT r.id, r.titre, r.contenu, r.rating, r.date,
                   u.id AS userid, u.nom AS username,
                   c.id AS cafeid, c.nom AS cafename
            FROM revues r
            JOIN utilisateurs u ON r.id_utilisateur = u.id
            JOIN cafes c ON r.id_cafe = c.id
            ORDER BY r.date DESC
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $reviewIds = array_column($reviews, 'id');

        $photosByReview = $this->getPhotosByReviewIds($reviewIds);
        $categoriesByReview = $this->getCategoriesByReviewIds($reviewIds);

        foreach ($reviews as &$review) {
            $id = $review['id'];
            $review['photos'] = $photosByReview[$id] ?? [];
            $review['categories'] = $categoriesByReview[$id] ?? [];
        }

        return $reviews;
    }

    public function fetchById($id)
    {
        $sql = "
            SELECT r.id, r.titre, r.contenu, r.rating, r.date,
                   u.id AS userid, u.nom AS username,
                   c.id AS cafeid, c.nom AS cafename
            FROM revues r
            JOIN utilisateurs u ON r.id_utilisateur = u.id
            JOIN cafes c ON r.id_cafe = c.id
            WHERE r.id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        return $review;
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

        $photosByReview = $this->getPhotosByReviewIds($reviewIds);

        foreach ($reviews as &$review) {
            $review['photos'] = $photosByReview[$review['id']] ?? [];
        }

        return $reviews;

    }

    public function fetchByCafeId(int $cafeId)
    {
        $sql = "SELECT r.*, u.nom AS username, c.nom AS cafename
            FROM revues r
            JOIN utilisateurs u ON r.id_utilisateur = u.id
            JOIN cafes c ON r.id_cafe = c.id
            WHERE r.id_cafe = :cafeId
            ORDER BY r.date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cafeId' => $cafeId]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $reviewIds = array_column($reviews, 'id');

        $photosByReview = $this->getPhotosByReviewIds($reviewIds);

        foreach ($reviews as &$review) {
            $review['photos'] = $photosByReview[$review['id']] ?? [];
        }

        return $reviews;
    }

    function fetchByCategoryId(int $categoryId)
    {
        $sql = "
            SELECT r.*, u.nom AS username, c.nom AS cafename
            FROM revues r
            JOIN utilisateurs u ON r.id_utilisateur = u.id
            JOIN cafes c ON r.id_cafe = c.id
            JOIN revues_categories rc ON r.id = rc.id_revue
            WHERE rc.id_categorie = :categoryId
            ORDER BY r.date DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':categoryId' => $categoryId]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $reviewIds = array_column($reviews, 'id');

        $photosByReview = $this->getPhotosByReviewIds($reviewIds);

        foreach ($reviews as &$review) {
            $review['photos'] = $photosByReview[$review['id']] ?? [];
        }

        return $reviews;
    }

    public function fetchFeed($userId)
    {
        $sql = "
        SELECT r.id, r.titre, r.contenu, r.description, r.rating, r.date,
               r.id_utilisateur, u.nom AS auteur_nom, c.nom AS cafe_nom
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

        $photosByReview = $this->getPhotosByReviewIds($reviewIds, true);
        $categoriesByReview = $this->getCategoriesByReviewIds($reviewIds);

        foreach ($reviews as &$review) {
            $review['thumbnail'] = $this->fetchThumbnailById($review['id']);
            $review['photos'] = $photosByReview[$review['id']] ?? [];
            $review['categories'] = $categoriesByReview[$review['id']] ?? [];
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

    public function fetchCategoriesById($id)
    {
        $sql = "
            SELECT cat.id, cat.nom
            FROM revues_categories rc
            JOIN categories cat ON rc.id_categorie = cat.id
            WHERE rc.id_revue = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $this->pdo->beginTransaction();
        try {
            $sql = "INSERT INTO revues (id_cafe, id_utilisateur, rating, contenu, description, titre)
                    VALUES (:id_cafe, :id_utilisateur, :rating, :contenu, :description, :titre)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_cafe' => $data['id_cafe'],
                ':id_utilisateur' => $data['id_utilisateur'],
                ':rating' => $data['rating'],
                ':contenu' => $data['contenu'],
                ':description' => ($data['description'] = mb_substr($data['contenu'], 0, 120) . '...') ?? null,
                ':titre' => $data['titre']
            ]);
            $reviewId = $this->pdo->lastInsertId();

            // If there are photos, insert each into photos_revue
            if (!empty($data['photos'])) {
                $stmtPhoto = $this->pdo->prepare(
                    "INSERT INTO photos_revue (id_revue, filepath, is_primary) VALUES (:id_revue, :filepath, :is_primary)"
                );

                foreach ($data['photos'] as $i => $photo) {
                    $stmtPhoto->execute([
                        ':id_revue' => $reviewId,
                        ':filepath' => $photo['filepath'],
                        ':is_primary' => ($i === 0) ? 1 : 0,
                    ]);
                }
            }

            if (!empty($data['categories'])) {
                $categories = $data['categories'];
                $sqlCategory = "INSERT INTO revues_categories (id_revue, id_categorie) VALUES (:id_revue, :id_categorie)";
                $stmtCategory = $this->pdo->prepare($sqlCategory);

                foreach ($categories as $categoryId) {
                    $stmtCategory->execute([
                        ':id_revue' => $reviewId,
                        ':id_categorie' => $categoryId
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
        $stmt->execute([
            ':titre' => $data['titre'],
            ':contenu' => $data['contenu'],
            ':rating' => $data['rating'],
            ':id' => $id
        ]);

        if (!empty($data['categories'])) {
            $deleteStmt = $this->pdo->prepare("DELETE FROM revues_categories WHERE id_revue = :id_revue");
            $deleteStmt->execute([':id_revue' => $id]);

            $insertStmt = $this->pdo->prepare("INSERT INTO revues_categories (id_revue, id_categorie) VALUES (:id_revue, :id_categorie)");
            foreach ($data['categories'] as $categoryId) {
                $insertStmt->execute([
                    ':id_revue' => $id,
                    ':id_categorie' => $categoryId
                ]);
            }
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM revues WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

}