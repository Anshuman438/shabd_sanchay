<?php
// api/get_comments.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$content_id = intval($_GET['id'] ?? $_GET['content_id'] ?? $_GET['poem_id'] ?? $_GET['article_id'] ?? 0);
$content_type = strtolower(trim($_GET['type'] ?? $_GET['content_type'] ?? 'poem'));

$valid_types = ['poem', 'article', 'story', 'play'];
if (!in_array($content_type, $valid_types)) {
    $content_type = 'poem';
}

$stmt = $conn->prepare("SELECT id, name, comment, created_at FROM comments WHERE content_id = ? AND content_type = ? AND status = 'approved' ORDER BY created_at DESC");
$stmt->bind_param("is", $content_id, $content_type);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $row['formatted_date'] = format_hindi_date($row['created_at']);
    $row['comment'] = nl2br(sanitize_text($row['comment']));
    $row['name'] = sanitize_text($row['name']);
    $comments[] = $row;
}
$stmt->close();

echo json_encode($comments, JSON_UNESCAPED_UNICODE);
?>