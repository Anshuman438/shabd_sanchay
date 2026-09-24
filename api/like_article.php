<?php
// api/like_article.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
$action = strtolower(trim($_GET['action'] ?? $_POST['action'] ?? 'like'));

if ($id <= 0) {
    send_json_response(false, 'अवैध अनुरोध (Invalid Request)', [], 400);
}

if ($action === 'unlike') {
    $stmt = $conn->prepare("UPDATE articles SET likes = GREATEST(0, likes - 1) WHERE id = ?");
} else {
    $stmt = $conn->prepare("UPDATE articles SET likes = likes + 1 WHERE id = ?");
}
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$stmt2 = $conn->prepare("SELECT likes FROM articles WHERE id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res = $stmt2->get_result()->fetch_assoc();
$newLikes = $res['likes'] ?? 0;
$stmt2->close();

$msg = ($action === 'unlike') ? 'पसंद हटा दी गई' : 'सफलतापूर्वक लाइक किया गया';
send_json_response(true, $msg, ['newLikes' => $newLikes, 'isLiked' => ($action !== 'unlike')]);
?>