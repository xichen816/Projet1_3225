<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

session_unset();
session_destroy();
header("Location: ../index.php");
echo json_encode(
    [
        "success" => true,
        "message" => "Déconnexion réussie."
    ]
);
http_response_code(200);
exit();
