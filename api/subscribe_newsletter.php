<?php
// api/subscribe_newsletter.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = trim($data['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json_response(false, 'कृपया एक वैध ईमेल पता दर्ज करें।', [], 400);
}

// Check duplicate
$check_stmt = $conn->prepare("SELECT id FROM newsletter_subscribers WHERE email = ? LIMIT 1");
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$res = $check_stmt->get_result();

if ($res && $res->num_rows > 0) {
    $check_stmt->close();
    send_json_response(true, 'आप पहले से ही शब्द संचय की पत्रिका से जुड़े हुए हैं।');
}
$check_stmt->close();

$stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    $stmt->close();
    send_json_response(true, 'धन्यवाद! आप सफलतापूर्वक पत्रिका के लिए सब्सक्राइब हो गए हैं।');
} else {
    $err = $stmt->error;
    $stmt->close();
    send_json_response(false, 'सब्सक्रिप्शन में त्रुटि: ' . $err, [], 500);
}
?>