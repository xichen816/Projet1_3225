<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');
require_once '../../config/config.php';
require_once '../../src/review.php';

$method = $_SERVER['REQUEST_METHOD'];
$review = new Review($pdo);

// Supports both JSON and FormData
function getInputData()
{
  if (strpos($_SERVER["CONTENT_TYPE"] ?? '', "application/json") !== false) {
    return json_decode(file_get_contents('php://input'), true);
  }
  return $_POST + $_FILES;
}
$input = getInputData();

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/uploads';
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0755, true);
}

switch ($method) {
  case 'GET':
    // Fetch all or single review by id
    try {
      if (isset($_GET['feed']) && isset($_GET['user_id'])) {
        $userId = intval($_GET['user_id']);
        $feed = $review->fetchFeed($userId);
        echo json_encode($feed);
        exit();
      }

      if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $reviewData = $review->fetchById($id);
        $photos = $review->fetchPhotosById($id);
        $thumbnail = $review->fetchThumbnailById($id);
        if ($reviewData) {
          $reviewData['photos'] = $photos;
          $reviewData['thumbnail'] = $thumbnail;
          echo json_encode($reviewData);
          exit();
        } else {
          echo json_encode([
            "success" => false,
            "message" => "Review not found."
          ]);
          exit();
        }
      }

      // Fetch reviews by user ID
      if (isset($_GET['user_id'])) {
        $userId = intval($_GET['user_id']);
        $userReviews = $review->fetchByUserId($userId);
        echo json_encode($userReviews);
        exit();
      }

      // Otherwise, fetch all reviews
      $reviews = $review->fetchAll();
      echo json_encode($reviews);
      exit();

    } catch (PDOException $e) {
      echo json_encode([
        "success" => false,
        "message" => "Review fetch error.",
        "error" => $e->getMessage()
      ]);
    }
    break;

  case 'POST':
    try {
      $input['id_utilisateur'] = $_SESSION['id'];
      $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/uploads/';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }
      $input['photos'] = [];

      if (isset($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
          if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
            $fileName = basename($_FILES['photos']['name'][$key]);
            $uniqueName = uniqid() . '-' . $fileName;
            $filePathAbs = $uploadDir . $uniqueName;
            $filePathRel = '/assets/images/uploads/' . $uniqueName;
            if (move_uploaded_file($tmp_name, $filePathAbs)) {
              $input['photos'][] = [
                'filepath' => $filePathRel,
                'type' => $_FILES['photos']['type'][$key],
                'size' => $_FILES['photos']['size'][$key]
              ];
            } else {
              throw new Exception("Failed to upload file: " . $fileName);
            }
          }
        }
      } elseif (isset($_FILES['photos']) && !is_array($_FILES['photos']['name'])) {
        if ($_FILES['photos']['error'] === UPLOAD_ERR_OK) {
          $fileName = basename($_FILES['photos']['name']);
          $uniqueName = uniqid() . '-' . $fileName;
          $filePathAbs = $uploadDir . $uniqueName;
          $filePathRel = '/assets/images/uploads/' . $uniqueName;
          if (move_uploaded_file($_FILES['photos']['tmp_name'], $filePathAbs)) {
            $input['photos'][] = [
              'filepath' => $filePathRel,
              'type' => $_FILES['photos']['type'],
              'size' => $_FILES['photos']['size']
            ];
          } else {
            throw new Exception("Failed to upload file: " . $fileName);
          }
        }
      } else {
        $input['photos'] = [];
      }

      $id = $review->create($input);
      echo json_encode(["success" => true, "review_id" => $id]);
    } catch (PDOException $e) {
      echo json_encode(["success" => false, "message" => "Add review error.", "error" => $e->getMessage()]);
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
    if (!isset($_GET['id'])) {
      http_response_code(400);
      echo json_encode(["success" => false, "message" => "Missing review ID"]);
      exit();
    }
    $reviewOwner = $review->getOwnerId($_GET['id']);
    if ($_SESSION['role'] === 'admin' || $_SESSION['id'] == $reviewOwner) {
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
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    break;
}
?>