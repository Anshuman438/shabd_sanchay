<?php
// api/get_featured_articles.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$query = "SELECT id, title, excerpt, content, author_name, category, read_time, image_url, views, likes, created_at 
          FROM articles 
          ORDER BY likes DESC, created_at DESC 
          LIMIT 3";
$result = $conn->query($query);

$articles = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['formatted_date'] = format_hindi_date($row['created_at']);
        if (empty($row['excerpt'])) {
            $row['excerpt'] = make_excerpt($row['content'], 160);
        }
        $articles[] = $row;
    }
}

echo json_encode($articles, JSON_UNESCAPED_UNICODE);
?>