<?php
header('Content-Type: application/json');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['content_id']) || !isset($data['content_type']) || !isset($data['name']) || !isset($data['email']) || !isset($data['comment'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

$content_id = intval($data['content_id']);
$content_type = $conn->real_escape_string($data['content_type']);
$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$comment = $conn->real_escape_string($data['comment']);

$query = "INSERT INTO comments (content_id, content_type, name, email, comment) 
          VALUES ($content_id, '$content_type', '$name', '$email', '$comment')";

if ($conn->query($query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$conn->close();
?>