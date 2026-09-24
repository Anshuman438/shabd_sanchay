<?php
// api/get_related_articles.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$article_id = intval($_GET['id'] ?? 0);
$category = trim($_GET['category'] ?? '');

if ($article_id <= 0) {
    echo json_encode([]);
    exit();
}

$stmt = $conn->prepare("SELECT id, title, author_name, category, image_url, excerpt, content, read_time, created_at FROM articles WHERE id != ? AND (category = ? OR ? = '') ORDER BY created_at DESC LIMIT 4");
$stmt->bind_param("iss", $article_id, $category, $category);
$stmt->execute();
$result = $stmt->get_result();

$articles = [];
while ($row = $result->fetch_assoc()) {
    $row['formatted_date'] = format_hindi_date($row['created_at']);
    if (empty($row['excerpt'])) {
        $row['excerpt'] = make_excerpt($row['content'], 140);
    }
    $articles[] = $row;
}
$stmt->close();

echo json_encode($articles, JSON_UNESCAPED_UNICODE);
?>