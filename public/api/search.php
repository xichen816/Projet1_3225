<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');
require_once '../../config/config.php';
require_once '../api/review.php';

$method = $_SERVER['REQUEST_METHOD'];
$review = new Review($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pfx = isset($_GET['pfx']) ? trim($_GET['pfx']) : '';
    $top = isset($_GET['top']) ? intval($_GET['top']) : 5;

    if (empty($pfx)) {
        echo json_encode([]);
        exit;
    }

    $prefix = "%$pfx%";
    $results = [];

    $stmt = $pdo->prepare("SELECT id, titre, contenu, 'review' as type FROM revues WHERE titre LIKE :pfx OR contenu LIKE :pfx LIMIT :top");
    $stmt->bindValue(':pfx', $prefix, PDO::PARAM_STR);
    $stmt->bindValue(':top', $top, PDO::PARAM_INT);
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $row['label'] = $row['titre'];
        $results[] = $row;
    }

    $stmt = $pdo->prepare("SELECT id, nom, 'cafe' as type FROM cafes WHERE nom LIKE :pfx LIMIT :top");
    $stmt->bindValue(':pfx', $prefix, PDO::PARAM_STR);
    $stmt->bindValue(':top', $top, PDO::PARAM_INT);
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $row['label'] = $row['nom'];
        $results[] = $row;
    }

    $stmt = $pdo->prepare("SELECT id, nom, 'user' as type FROM utilisateurs WHERE nom LIKE :pfx LIMIT :top");
    $stmt->bindValue(':pfx', $prefix, PDO::PARAM_STR);
    $stmt->bindValue(':top', $top, PDO::PARAM_INT);
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $row['label'] = $row['nom'];
        $results[] = $row;
    }

    $stmt = $pdo->prepare("SELECT id, nom, 'category' as type FROM categories WHERE nom LIKE :pfx LIMIT :top");
    $stmt->bindValue(':pfx', $prefix, PDO::PARAM_STR);
    $stmt->bindValue(':top', $top, PDO::PARAM_INT);
    $stmt->execute();
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $row['label'] = $row['nom'];
        $results[] = $row;
    }
    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Search fetch error.",
        "error" => $e->getMessage()
    ]);
}

?>