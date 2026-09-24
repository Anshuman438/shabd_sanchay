<?php
// api/add_comment.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$content_id = intval($input['content_id'] ?? $input['poem_id'] ?? $input['article_id'] ?? 0);
$content_type = strtolower(trim($input['content_type'] ?? 'poem'));

$valid_types = ['poem', 'article', 'story', 'play'];
if (!in_array($content_type, $valid_types)) {
    $content_type = 'poem';
}

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$comment = trim($input['comment'] ?? '');

if ($content_id <= 0 || empty($name) || empty($email) || empty($comment)) {
    send_json_response(false, 'कृपया सभी आवश्यक फ़ील्ड भरें।', [], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json_response(false, 'कृपया एक वैध ईमेल पता दर्ज करें।', [], 400);
}

// Default to 'approved' for active discussion, can be moderated from admin
$status = 'approved';

$stmt = $conn->prepare("INSERT INTO comments (content_id, content_type, name, email, comment, status) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    send_json_response(false, 'डेटाबेस त्रुटि: ' . $conn->error, [], 500);
}

$stmt->bind_param("isssss", $content_id, $content_type, $name, $email, $comment, $status);

if ($stmt->execute()) {
    $new_id = $stmt->insert_id;
    $stmt->close();
    send_json_response(true, 'आपकी टिप्पणी सफलतापूर्वक प्रकाशित हो गई है।', [
        'id' => $new_id,
        'name' => sanitize_text($name),
        'comment' => nl2br(sanitize_text($comment)),
        'date' => format_hindi_date(date('Y-m-d H:i:s'))
    ]);
} else {
    $error = $stmt->error;
    $stmt->close();
    send_json_response(false, 'टिप्पणी जोड़ने में विफलता: ' . $error, [], 500);
}
?>