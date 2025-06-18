<?php
require_once "config/config.php";

if (isset($_SESSION["email"]) && isset($_SESSION["token"])) {
    $requete = $pdo->prepare("SELECT token FROM utilisateurs WHERE email = :email");
    $requete->execute(['email' => $_SESSION['email']]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && $utilisateur['token'] === $_SESSION['token']) {
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: client.php");
        }
        exit();
    } else {
        session_destroy();
        header("Location: connexion.php");
        exit();
    }
}

$error_msg = "";

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

            if ($utilisateur['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: client.php");
            }
            exit();
        } else {
            $error_msg = "Email ou mot de passe incorrect.";
        }
    } else {
        $error_msg = "Veuillez remplir tous les champs.";
    }
}