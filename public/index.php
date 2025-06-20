<?php
session_start();
if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
    // User is authenticated, proceed with the request
    echo "You are authenticated.";
    // Redirect to the feed page
    header("Location: feed.php");
    exit();
} else {
    // User is not authenticated, but session exists
    echo "You are not authenticated.";
    header("Location: explore.php");
    exit();
}
?>