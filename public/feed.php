<?php
session_start();
// TODO : Navbar (Search bar, Filters, Hamburger menu (profile, settings, logout), Two columns (user feed (recent reviews, followed profiles), user reviews))
require_once "config/config.php";
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
                <input type="text" placeholder="Search...">
                <button type="submit">Search</button>
            </div>
            <div class="hambuger-menu"></div>

        </ul>

    </div>
    <div class="content">
        <div class="content-columns">
            <div class="user-feed"></div>
            <div class="user-reviews"></div>
        </div>

    </div>
</body>

</html>