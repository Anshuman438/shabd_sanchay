<?php
header('Content-Type: application/json');
require_once '../config.php';

$query = "SELECT *, content as poem_content FROM poems ORDER BY likes DESC, created_at DESC LIMIT 3";
$result = $conn->query($query);

$poems = [];
while ($row = $result->fetch_assoc()) {
    $row['poem_content'] = nl2br(htmlspecialchars($row['poem_content']));
    $poems[] = $row;
}

echo json_encode($poems);
$conn->close();
?>