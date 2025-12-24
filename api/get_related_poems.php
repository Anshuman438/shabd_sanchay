<?php
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_GET['id']) || !isset($_GET['category'])) {
    echo json_encode([]);
    exit();
}

$poem_id = intval($_GET['id']);
$category = $conn->real_escape_string($_GET['category']);

$query = "SELECT * FROM poems WHERE id != $poem_id AND category = '$category' ORDER BY RAND() LIMIT 5";
$result = $conn->query($query);

$poems = [];
while ($row = $result->fetch_assoc()) {
    $poems[] = $row;
}

echo json_encode($poems);
$conn->close();
?>