<?php
class Review {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getOwnerId($reviewId) {
        $sql = 'SELECT id_utilisateur FROM revues WHERE id = :reviewId';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id_utilisateur'] : null;
    }

    public function fetchAll() {
    $sql = "
        SELECT r.id, r.titre, r.contenu, r.rating, r.date, r.id_cafe, r.id_utilisateur, r.id_categorie, r.image_url
        FROM revues r
        JOIN utilisateurs u ON r.id_utilisateur = u.id
        JOIN cafes c ON r.id_cafe = c.id
        LEFT JOIN categories cat ON r.id_categorie = cat.id
        ORDER BY r.date DESC
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($reviews as &$review) {
        $reviewId = $review['id'];
        $catSql = "SELECT cat.nom FROM revues_categories rc
                   JOIN categories cat ON rc.id_categorie = cat.id
                   WHERE rc.id_revue = :id_revue";
        $catStmt = $this->pdo->prepare($catSql);
        $catStmt->execute([':id_revue' => $reviewId]);
        $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
        $review['categories'] = array_map(function($cat) { return $cat['nom']; }, $categories);
    }

    return $reviews;
}

    public function create($data) {
        $sql = "INSERT INTO revues (id_cafe, id_utilisateur, id_categorie, rating, contenu, titre, image_url)
                VALUES (:id_cafe, :id_utilisateur, :id_categorie, :rating, :contenu, :titre, :image_url)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_cafe' => $data['id_cafe'],
            ':id_utilisateur' => $data['id_utilisateur'],
            ':rating' => $data['rating'],
            ':contenu' => $data['contenu'],
            ':titre' => $data['titre'],
            ':image_url' => $data['image_url'] ?? null,
        ]);
        $reviewId = $this->pdo->lastInsertId();

        if (!empty($data['categorie'])) {
            $categories = $data['categorie'];
            $sqlCategory = "INSERT INTO revues_categories (id_revue, id_categorie) VALUES (:id_revue, :id_categorie)";
            $stmtCategory = $this->pdo->prepare($sqlCategory);

            foreach ($categories as $categoryId) {
                $stmtCategory->execute([
                    ':id_revue' => $reviewId,
                    ':id_categorie' => $categoryId
                ]);
            }
        }
        return $reviewId;
    }

    public function update($id, $data) {
        $sql = "UPDATE revues SET titre = :titre, contenu = :contenu, rating = :rating, image_url = :image_url,id_categorie = :id_categorie
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':contenu' => $data['contenu'],
            ':rating' => $data['rating'],
            ':image_url' => $data['image_url'] ?? null,
            ':id_categorie' => $data['id_categorie'] ?? null,
            ':id' => $id
        ]);

        if (!empty($data['categorie'])) {
            $deleteStmt = $this->pdo->prepare("DELETE FROM revues_categories WHERE id_revue = :id_revue");
            $deleteStmt->execute([':id_revue' => $id]);

            $insertStmt = $this->pdo->prepare("INSERT INTO revues_categories (id_revue, id_categorie) VALUES (:id_revue, :id_categorie)");
            foreach ($data['categorie'] as $categoryId) {
                $insertStmt->execute([
                    ':id_revue' => $id,
                    ':id_categorie' => $categoryId
                ]);
    }
}
    }
    
    public function delete($id) {
        $sql = "DELETE FROM revues WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

}