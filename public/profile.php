<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// TODO : User information, like name, email, profile picture, etc.
// TODO : User reviews, like recent reviews, edit or delete reviews, etc.
// TODO : Add styling
require_once "../config/config.php";
require_once "../includes/auth.php";
requireAuth();
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
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">
        <!-- <img src="../assets/icon/cafe-run-icon.png" width="32" height="32"> -->
        Cafe Run
      </a>
      <form class="d-flex mx-auto">
        <input class="form-control me-2" type="search" placeholder="Recherche..." aria-label="Recherche" disabled>
        <button class="btn btn-outline-success" type="submit" disabled>Rechercher</button>
      </form>
      <div class="d-flex">
        <!-- Hamburger menu for profile and settings -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-list"></i> <!-- or just “Menu” -->
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="profile.php">Profil</a></li>
            <li><a class="dropdown-item" href="../public/feed.php">Feed</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" id="logout" href="../public/decconnexion.php">Déconnexion</a></li>
          </ul>
      </div>
    </div>
  </nav>

    <script src="../assets/js/ajax.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script>
        document.getElementById('logout').addEventListener('click', function (event) {
            event.preventDefault();
            logoutUser();
        });
    </script>
</body>

</html>