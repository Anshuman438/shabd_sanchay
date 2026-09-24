<?php
// api/get_featured_poems.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$query = "SELECT id, title, content as poem_content, author_name, category, image_url, views, likes, created_at 
          FROM poems 
          ORDER BY likes DESC, created_at DESC 
          LIMIT 3";
$result = $conn->query($query);

$poems = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['formatted_date'] = format_hindi_date($row['created_at']);
        $poems[] = $row;
    }
}

echo json_encode($poems, JSON_UNESCAPED_UNICODE);
?>