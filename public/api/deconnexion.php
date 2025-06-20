<?php
session_start();
session_unset();
session_destroy();
// header("Location: connexion.php");
echo json_encode(
    [
        "success" => true,
        "message" => "Déconnexion réussie."
    ]
);
http_response_code(200);
exit();
