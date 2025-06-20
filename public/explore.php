<?php
session_start();
$authenticated = false;
if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
    // User is authenticated, proceed with the request
    echo "You are authenticated.";
    $authenticated = true;
} else {
    // User is not authenticated, redirect to login page
    $authenticated = false;
    // header("Location: connexion.php");
    // exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cafe Run</title>
</head>

<body>
    <div class="top-nav">
        <ul>
            <li><img src="../assets/icon/cafe-run-icon.png">Home</a></li>
            <div class="search-bar">
                <input type="text" placeholder="Search..." disabled>
                <button type="submit" disabled>Search</button>
            </div>
            <li><a href="connexion.php">Sign up</a></li>
            <li><a href="connexion.php">Login</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="content-page">
            <div class="explore"></div>
        </div>
    </div>
</body>

</html>