<?php
header('Content-Type: application/json');
require_once '../config.php';

$content_type = isset($_GET['poem_id']) ? 'poem' : 'article';
$content_id = isset($_GET['poem_id']) ? intval($_GET['poem_id']) : intval($_GET['article_id']);

$query = "SELECT * FROM comments WHERE content_type = '$content_type' AND content_id = $content_id ORDER BY created_at DESC";
$result = $conn->query($query);

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode($comments);
$conn->close();
?>