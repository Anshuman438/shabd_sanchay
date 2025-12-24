<?php
header('Content-Type: application/json');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit();
}

$email = $conn->real_escape_string($data['email']);

// Check if already subscribed
$check = $conn->query("SELECT id FROM newsletter_subscribers WHERE email = '$email'");
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'आप पहले से ही सब्सक्राइब हैं']);
    exit();
}

$query = "INSERT INTO newsletter_subscribers (email) VALUES ('$email')";

if ($conn->query($query)) {
    echo json_encode(['success' => true, 'message' => 'सब्सक्रिप्शन सफल']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$conn->close();
?>