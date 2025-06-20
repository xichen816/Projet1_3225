<?php
// Session initialization and user authentication
require_once "../config/config.php";
if(session_status() == PHP_SESSION_NONE) {
    session_start();

}

$_SESSION['authenticated'] = false;

if(!isset($_SESSION['email']) || !isset($_SESSION['token'])) {
    header("Location: connexion.php");
    exit();
}

$stmt = $pdo-> prepare("SELECT token FROM utilisateurs WHERE email = :email");
$stsm->execute(['email' => $_SESSION['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$user || $user['token'] !== $_SESSION['token']) {
    session_destroy();
    header("Location: connexion.php");
    exit();
} else {
    $_SESSION['authenticated'] = true;
    $_SESSION['role'] = $user['role'] ?? 'utilisateur';
    $_SESSION['id'] = $user['id'] ?? null;
}
?>