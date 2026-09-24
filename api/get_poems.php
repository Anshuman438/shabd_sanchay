<?php
// api/get_poems.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$category = $_GET['category'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$sql = "SELECT id, title, content, author_name, category, image_url, views, likes, created_at FROM poems WHERE 1=1";
$params = [];
$types = "";

if ($category !== 'all' && !empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR content LIKE ? OR author_name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
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

$poems = [];
while ($row = $result->fetch_assoc()) {
    $row['content'] = normalize_content_text($row['content']);
    $row['formatted_date'] = format_hindi_date($row['created_at']);
    $row['preview'] = make_excerpt($row['content'], 180);
    $poems[] = $row;
}
$stmt->close();

echo json_encode($poems, JSON_UNESCAPED_UNICODE);
?>