<?php
header('Content-Type: application/json');
require_once '../config.php';

$query = "SELECT * FROM articles ORDER BY created_at DESC";
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