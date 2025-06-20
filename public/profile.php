<?php
require_once "config/config.php";

if (!isset($_SESSION['email']) || !isset($_SESSION['token'])) {
    header("Location: connexion.php");
    exit();
}
?>