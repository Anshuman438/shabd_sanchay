<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_GET['id']) || !isset($_GET['category'])) {
    echo json_encode([]);
    exit();
}

$article_id = intval($_GET['id']);
$category = $conn->real_escape_string($_GET['category']);

$query = "SELECT * FROM articles WHERE id != $article_id AND category = '$category' ORDER BY RAND() LIMIT 5";
$result = $conn->query($query);

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}

echo json_encode($articles);
$conn->close();
?>