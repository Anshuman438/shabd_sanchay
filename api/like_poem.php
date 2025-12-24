<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$poem_id = intval($_GET['id']);
$conn->query("UPDATE poems SET likes = likes + 1 WHERE id = $poem_id");

// Get new like count
$result = $conn->query("SELECT likes FROM poems WHERE id = $poem_id");
$newLikes = $result->fetch_assoc()['likes'];

echo json_encode(['success' => true, 'newLikes' => $newLikes]);
$conn->close();
?>