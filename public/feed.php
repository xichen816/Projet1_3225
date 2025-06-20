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
            <?php if ($authenticated): ?>
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                    <button type="submit">Search</button>
                </div>
                <div class="hambuger-menu"></div>
            <?php else: ?>
                <div class="search-bar">
                    <input type="text" placeholder="Search..." disabled>
                    <button type="submit" disabled>Search</button>
                </div>
                <li><a href="connexion.php">Sign up</a></li>
                <li><a href="connexion.php">Login</a></li>

            <?php endif; ?>
        </ul>

    </div>
    <div class="content">
        <?php if ($authenticated): ?>
            <div class="content-columns">
                <div class="user-feed"></div>
                <div class="user-reviews"></div>
            </div>
        <?php else: ?>
            <div class="content-page">
                <div class="explore"></div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>