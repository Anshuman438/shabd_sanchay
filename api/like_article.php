<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$article_id = intval($_GET['id']);
$conn->query("UPDATE articles SET likes = likes + 1 WHERE id = $article_id");

// Get new like count
$result = $conn->query("SELECT likes FROM articles WHERE id = $article_id");
$newLikes = $result->fetch_assoc()['likes'];

echo json_encode(['success' => true, 'newLikes' => $newLikes]);
$conn->close();
?>