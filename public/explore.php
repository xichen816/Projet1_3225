<?php
session_start();
// TODO : Navbar (Login, Sign up, Search bar? Filters?)
// TODO : Review tiles : Explore section with tiles of reviews
// TODO : Pagination, Load more, page buttons, etc...
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