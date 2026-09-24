<?php
// api/get_articles.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$category = $_GET['category'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$sql = "SELECT id, title, excerpt, content, author_name, category, read_time, image_url, views, likes, created_at FROM articles WHERE 1=1";
$params = [];
$types = "";

if ($category !== 'all' && !empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR content LIKE ? OR author_name LIKE ? OR excerpt LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ssss";
}

switch ($sort) {
    case 'popular':
        $sql .= " ORDER BY likes DESC, views DESC, created_at DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY created_at DESC";
        break;
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$articles = [];
while ($row = $result->fetch_assoc()) {
    $row['content'] = normalize_content_text($row['content']);
    $row['formatted_date'] = format_hindi_date($row['created_at']);
    if (empty($row['excerpt'])) {
        $row['excerpt'] = make_excerpt($row['content'], 160);
    } else {
        $row['excerpt'] = normalize_content_text($row['excerpt']);
    }
    $articles[] = $row;
}
$stmt->close();

echo json_encode($articles, JSON_UNESCAPED_UNICODE);
?>