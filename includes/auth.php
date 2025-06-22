<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';

function isAuthenticated() {
    return !empty($_SESSION['email']) && !empty($_SESSION['token']) && isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

function requireAuth() {
    if (!isAuthenticated()) {
        header("Location: ../public/connexion.php");
        exit();
    }
}

function getCurrentUserId($pdo) {
    if (!empty($_SESSION['email'])) {
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $_SESSION['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'] ?? null;
    }
    return null;
}

if (!isAuthenticated()) {
    if (!empty($_SESSION['email']) && !empty($_SESSION['token'])) {
        $stmt = $pdo->prepare("SELECT id, role, token FROM utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $_SESSION['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            $user &&
            hash_equals($user['token'], $_SESSION['token'])
        ) {
            $_SESSION['authenticated'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
        } else {
            session_unset();
            session_destroy();
            header("Location: ../public/connexion.php");
            exit();
        }
    } else {
        header("Location: ../public/connexion.php");
        exit();
    }
}
?>
