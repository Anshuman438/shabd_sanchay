<?php
// api/like_play.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
$action = strtolower(trim($_GET['action'] ?? $_POST['action'] ?? 'like'));

if ($id <= 0) {
    send_json_response(false, 'अवैध अनुरोध (Invalid Request)', [], 400);
}

$res = toggle_user_like($conn, 'play', $id, $action);
send_json_response(true, $res['message'], ['newLikes' => $res['newLikes'], 'isLiked' => $res['isLiked']]);
?>
