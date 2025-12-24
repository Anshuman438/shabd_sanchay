<?php
header('Content-Type: application/json');
require_once '../config.php';

$category = $_GET['category'] ?? 'all';
$sort = $_GET['sort'] ?? 'newest';
$search = $_GET['search'] ?? '';

// Start query
$query = "SELECT * FROM poems WHERE 1=1";

// Filter by category
if ($category !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

// Search
if (!empty($search)) {
    $query .= " AND (title LIKE ? OR content LIKE ? OR author_name LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

// Sort
switch ($sort) {
    case 'oldest':
        $query .= " ORDER BY created_at ASC";
        break;
    case 'popular':
        $query .= " ORDER BY likes DESC";
        break;
    default:
        $query .= " ORDER BY created_at DESC";
}

// Prepare and execute
$stmt = $conn->prepare($query);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$poems = [];
while ($row = $result->fetch_assoc()) {
    $row['content'] = nl2br(htmlspecialchars($row['content']));
    $poems[] = $row;
}

echo json_encode($poems);
$conn->close();
?>
