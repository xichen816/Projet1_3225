<?php
session_start();
if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
  // User is authenticated, proceed with the request
  echo "You are authenticated.";
  // Redirect to the feed page
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
  <div class="top-nav">
    <ul>
      <li><img src="../assets/icon/cafe-run-icon.png" alt="Cafe Run Icon">Home</li>
      <div class="search-bar">
        <input type="text" placeholder="Search..." disabled>
        <button type="submit" disabled>Search</button>
      </div>
      <li><a href="inscription.php">S'inscrire</a></li>
      <li><a href="connexion.php">Se connecter</a></li>
    </ul>
  </div>
  <?php include 'explore.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
    crossorigin="anonymous"></script>
</body>

</html>