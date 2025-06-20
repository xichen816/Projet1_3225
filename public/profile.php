<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// TODO : User information, like name, email, profile picture, etc.
// TODO : User reviews, like recent reviews, edit or delete reviews, etc.
// TODO : Add styling
require_once "../config/config.php";
require_once "../includes/auth.php";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Cafe Run</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->
    <!-- <link rel="icon" href="../assets/icon/cafe-run-icon.png"> -->
</head>

<body>
    <!-- TODO : Put this in a component in includes/navbar.php -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <!-- <img src="../assets/icon/cafe-run-icon.png" width="32" height="32"> -->
            Cafe Run
        </a>
        <form class="form-inline my-2 my-lg-0 mx-3">
            <input class="form-control mr-sm-2" type="search" placeholder="Recherche..." aria-label="Recherche">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
        </form>
        <div class="ml-auto">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= htmlspecialchars($_SESSION['email']) ?>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <!-- <a class="dropdown-item" href="#">Profil</a> -->
                    <!-- <a class="dropdown-item" href="#">Paramètres</a> -->
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="logoutUser(); return false;">Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <script src="../assets/js/ajax.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script></script>
</body>

</html>