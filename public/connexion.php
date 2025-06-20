<?php
session_start();
require_once "../config/config.php";
if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
    header("Location: feed.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Cafe Run</title>
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h2>Connexion</h2>
        <form id="connexionForm">
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        <div id="message" class="mt-3"></div>
        <div class="mt-3">
            <p>Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous ici</a>.</p>
        </div>
    </div>
    <script src="../assets/js/ajax.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script>
        document.getElementById('connexionForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const email = this.email.value;
            const password = this.password.value;
            loginUser(email, password);
        });
    </script>

</body>

</html>