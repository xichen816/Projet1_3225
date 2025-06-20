<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    // Fetch reviews logic
    echo json_encode($reviews);
    break;

  case 'POST':
    // Add review logic
    echo json_encode(["success" => true, "review_id" => $id]);
    break;

  case 'PUT':
    // Update review logic
    echo json_encode(["updated" => true]);
    break;

  case 'DELETE':
    // Delete review logic
    echo json_encode(["deleted" => true]);
    break;

  default:
    http_response_code(405); // Method Not Allowed
    break;
}
?>
