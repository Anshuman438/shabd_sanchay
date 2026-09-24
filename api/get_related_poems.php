<?php
// api/get_related_poems.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$poem_id = intval($_GET['id'] ?? 0);
$category = trim($_GET['category'] ?? '');

if ($poem_id <= 0) {
    echo json_encode([]);
    exit();
}

$poems = [];
$found_ids = [$poem_id];

// 1. Fetch poems in same category
if (!empty($category)) {
    $stmt = $conn->prepare("SELECT id, title, author_name, category, image_url, content, views, likes, created_at FROM poems WHERE id != ? AND category = ? ORDER BY created_at DESC LIMIT 3");
    if ($stmt) {
        $stmt->bind_param("is", $poem_id, $category);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $row['formatted_date'] = format_hindi_date($row['created_at']);
            $poems[] = $row;
            $found_ids[] = (int)$row['id'];
        }
        $stmt->close();
    }
}

// 2. If fewer than 3, fill with other poems
if (count($poems) < 3) {
    $needed = 3 - count($poems);
    $placeholders = implode(',', array_fill(0, count($found_ids), '?'));
    $types = str_repeat('i', count($found_ids)) . 'i';
    
    $query = "SELECT id, title, author_name, category, image_url, content, views, likes, created_at FROM poems WHERE id NOT IN ($placeholders) ORDER BY (likes + views) DESC, created_at DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $params = array_merge($found_ids, [$needed]);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $row['formatted_date'] = format_hindi_date($row['created_at']);
            $poems[] = $row;
        }
        $stmt->close();
    }
}

echo json_encode($poems, JSON_UNESCAPED_UNICODE);
?>