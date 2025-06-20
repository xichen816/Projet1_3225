<?php
session_start();
header("Content-Type: application/json");
require_once "../../config/config.php";

if (isset($_SESSION["email"]) && isset($_SESSION["token"])) {
    $requete = $pdo->prepare("SELECT token FROM utilisateurs WHERE email = :email");
    $requete->execute(['email' => $_SESSION['email']]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && $utilisateur['token'] === $_SESSION['token']) {
        echo json_encode(
            [
                "success" => true,
                "message" => "Déjà connecté.",
                "role" => $_SESSION['role'],
                "userId" => $_SESSION['id'],
                "nom" => $utilisateur['nom'] ?? null,
            ]
        );
        exit();
    } else {
        session_destroy();
        echo json_encode(
            [
                "success" => false,
                "message" => "Session expirée ou invalide. Veuillez vous reconnecter.",
                "role" => null,
                "userId" => null,
            ]
        );
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    if (!empty($email) && !empty($password)) {
        $requete = $pdo->prepare("SELECT id, mot_de_passe, role, token FROM utilisateurs WHERE email = :email");
        $requete->execute(['email' => $email]);
        $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur && password_verify($password, $utilisateur['mot_de_passe'])) {
            $token = bin2hex(random_bytes(32));

            $update = $pdo->prepare("UPDATE utilisateurs SET token = :token WHERE email = :email");
            $update->execute(['token' => $token, 'email' => $email]);

            $_SESSION['id'] = $utilisateur['id'];
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $utilisateur['role'];
            $_SESSION['token'] = $token;

            echo json_encode(
                [
                    "success" => true,
                    "message" => "Connexion réussie.",
                    "role" => $utilisateur['role'],
                    "userId" => $utilisateur['id']
                ]
            );
            exit();
        } else {
            json_encode(
                [
                    "success" => false,
                    "message" => "Identifiants incorrects. Veuillez réessayer.",
                    "role" => null,
                    "userId" => null
                ]
            );
            exit();
        }
    } else {
        json_encode(
            [
                "success" => false,
                "message: Veuillez remplir tous les champs.",
                "role" => null,
                "userId" => null,
            ]
        );
        exit();
    }
} else {
    echo json_encode(
        [
            "success" => false,
            "message" => "Méthode de requête non autorisée.",
            "role" => null,
            "userId" => null
        ]
    );
    exit();
}