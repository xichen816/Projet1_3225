<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
    header("Location: feed.php");
    exit();
}
?>
<!Doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Cafe Run</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->
</head>

<body>
    <div class="container mt-5">
        <h2>Inscription</h2>
        <form id="inscriptionForm">
            <div class="form-group mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required minlength="8">
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
        <div id="message"></div>
        <div class="mt-3">
            <p>Déjà inscrit ? <a href="connexion.php">Connectez-vous ici</a>.</p>
        </div>

        <script src="../assets/js/ajax.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
            crossorigin="anonymous"></script>
        <script>
            document.getElementById('inscriptionForm').addEventListener('submit', function (event) {
                event.preventDefault();
                const nom = this.nom.value;
                const email = this.email.value;
                const password = this.password.value;
                signUpUser(nom, email, password);
            });
</body >

</html >