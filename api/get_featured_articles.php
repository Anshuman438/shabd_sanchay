<?php
header('Content-Type: application/json');
require_once '../config.php';

$query = "SELECT * FROM articles ORDER BY likes DESC, created_at DESC LIMIT 3";
$result = $conn->query($query);

$articles = [];
while ($row = $result->fetch_assoc()) {
    $row['content'] = nl2br(htmlspecialchars($row['content']));
    $row['excerpt'] = nl2br(htmlspecialchars($row['excerpt']));
    $articles[] = $row;
}

echo json_encode($articles);
$conn->close();
?>