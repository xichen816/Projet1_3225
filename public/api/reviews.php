<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../../config/config.php';
require_once '../api/review.php';

$method = $_SERVER['REQUEST_METHOD'];
$review = new Review($pdo);

$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
  case 'GET':
    // Fetch reviews logic
    try {
      $reviews = $review->fetchAll();
      echo json_encode($reviews);
    } catch (PDOException $e) {
      echo json_encode([
        "success" => false, 
        "message" => "Review fetch error.",
        "error" => $e->getMessage()
        ]);
    }
    break;

  case 'POST':
    // Add review logic
    try {
      $id = $review->create($input);
      echo json_encode(["success" => true, "review_id" => $id]);
    } catch (PDOException $e) {
      echo json_encode(["success" => false, "message" => "Add review error."]);
    }
    break;

  case 'PUT':
    // Update review logic
    if (!isset($input['id'])) {
      http_response_code(400);
      echo json_encode(["success" => false, "message" => "Missing review ID"]);
      break;
    }
    if ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] === $review->getOwnerId($reviewId)) {
      try {
        $success = $review->update($input['id'], $input);
        echo json_encode(["updated" => $success]);
      } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Update review error."]);
      }
    } else {
      http_response_code(403);
      echo json_encode(["success" => false, "message" => "Unauthorized"]);
    }
    break;

  case 'DELETE':
    // Delete review logic
    if (!isset($_GET['id'])) {
      http_response_code(400);
      echo json_encode(["success" => false, "message" => "Missing review ID"]);
      exit();
    }
    if ($_SESSION['role'] === 'admin' || $_SESSION['user_id'] === $review->getOwnerId($reviewId)) {
      try {
        $success = $review->delete($_GET['id']);
        echo json_encode(["deleted" => $success]);
      } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Delete review error."]);
      }
    }
    break;

  default:
    http_response_code(405); // Method Not Allowed
    break;
}

