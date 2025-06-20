<?php
session_start();
// TODO : User information, like name, email, profile picture, etc.
// TODO : User reviews, like recent reviews, edit or delete reviews, etc.
// TODO : Add styling
require_once "config/config.php";

if (!isset($_SESSION['email']) || !isset($_SESSION['token'])) {
    header("Location: connexion.php");
    exit();
}
?>