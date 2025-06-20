<?php
require_once "config/config.php";

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    $requete_email = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
    $requete_email->execute(['email' => $email]);
    $email_exist = $requete_email->fetchColumn();

    if ($email_exist > 0) {
        $error_msg = "Ce courriel est déjà utilisé !";
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

            $success_msg = "Inscription réussie !";
            header("Location: connexion.php");
            exit();
        } catch (PDOException $e) {
            $error_msg = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>