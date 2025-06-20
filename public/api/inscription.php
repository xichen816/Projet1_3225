<?php
session_start();
header("Content-Type: application/json");
require_once "../../config/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    if (!$nom || !$email || !$password) {
        echo json_encode(["success" => false, "message" => "Veuillez remplir tous les champs."]);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Adresse e-mail invalide."]);
        exit();
    }
    if (strlen($password) < 8) {
        echo json_encode(["success" => false, "message" => "Le mot de passe doit contenir au moins 8 caractères."]);
        exit();
    }
    if (strlen($nom) < 1 || strlen($nom) > 50) {
        echo json_encode(["success" => false, "message" => "Le nom doit contenir entre 1 et 50 caractères."]);
        exit();
    }

    $requete_email = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
    $requete_email->execute(['email' => $email]);
    $email_exist = $requete_email->fetchColumn();

    if ($email_exist > 0) {
        echo json_encode(["success" => false, "message" => "Ce courriel est déjà utilisé."]);
        exit();
    } else {
        try {
            $mot_de_passe_hache = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16));

            $requete = $pdo->prepare("
                INSERT INTO utilisateurs (nom, email, mot_de_passe, role, token) 
                VALUES (:nom, :email, :mot_de_passe, :role, :token)
            ");
            $requete->execute([
                "nom" => $nom,
                "email" => $email,
                "mot_de_passe" => $mot_de_passe_hache,
                "role" => "utilisateur",
                "token" => $token
            ]);

            if ($requete->rowCount() > 0) {
                echo json_encode(["success" => true, "message" => "Inscription réussie !"]);

            } else {
                echo json_encode(["success" => false, "message" => "Erreur lors de l'inscription"]);
            }
            exit();
        } catch (PDOException $e) {
            $error_msg = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Méthode de requête non autorisée."]);
    exit();
}
?>