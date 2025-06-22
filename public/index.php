<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once "../includes/auth.php";
requireAuth();

if (!empty($_SESSION['authenticated']) && $_SESSION["authenticated"] === true) {
  header("Location: feed.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Explore - Cafe Run</title>
  <!-- TODO : Add CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <!-- <link rel="icon" href="../assets/icon/cafe-run-icon.png"> -->
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <!-- <img src="../assets/icon/cafe-run-icon.png" width="32" height="32"> -->
        Cafe Run
      </a>
      <form class="d-flex mx-auto">
        <input class="form-control me-2" type="search" placeholder="Recherche..." aria-label="Recherche" disabled>
        <button class="btn btn-outline-success" type="submit" disabled>Rechercher</button>
      </form>
      <div class="d-flex">
        <button class="btn btn-primary me-2">
          <a href="inscription.php" class="text-white text-decoration-none">S'inscrire</a>
        </button>
        <button class="btn btn-primary">
          <a href="connexion.php" class="text-white text-decoration-none">Se connecter</a>
        </button>
      </div>
    </div>
  </nav>
  <?php include 'explore.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
    crossorigin="anonymous"></script>
</body>

</html>