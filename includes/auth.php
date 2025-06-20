<?php
// Session initialization and user authentication
session_start();

if (isset($_SESSION[""]) && $_SESSION["authenticated"] == true) {
    echo "You are authenticated.";
    header("Location: feed.php");
    exit();
} else {
    echo "You are not authenticated.";
    header("Location: explore.php");
    exit();
}
?>