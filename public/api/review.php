<?php
class Review {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function fetchAll() {
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO revues (id_cafe, id_utilisateur, rating, contenu, titre)
                VALUES (:id_cafe, :id_utilisateur, :rating, :contenu, :titre)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_cafe' => $data['id_cafe'],
            ':id_utilisateur' => $data['id_utilisateur'],
            ':rating' => $data['rating'],
            ':contenu' => $data['contenu'],
            ':titre' => $data['titre'],
        ]);
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE revues SET titre = :titre, contenu = :contenu, rating = :rating
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':contenu' => $data['contenu'],
            ':rating' => $data['rating'],
            ':id' => $id
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM revues WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

}