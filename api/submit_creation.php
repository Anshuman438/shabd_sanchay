<?php
// api/submit_creation.php - Handle User Content Submission
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'केवल POST अनुरोध स्वीकार्य हैं।', [], 405);
}

// Get logged in user if any
$logged_user = get_logged_in_user($conn);
$user_id = $logged_user['id'] ?? null;

// Collect inputs from JSON or POST/Files
$content_type = strtolower(trim($_POST['content_type'] ?? 'poem'));
$valid_types = ['poem', 'article', 'story', 'play'];
if (!in_array($content_type, $valid_types)) {
    send_json_response(false, 'अमान्य रचना विधा (Invalid content type)', [], 400);
}

$title = trim($_POST['title'] ?? '');
$author_name = trim($_POST['author_name'] ?? ($logged_user['name'] ?? ''));
$author_email = trim($_POST['author_email'] ?? ($logged_user['email'] ?? ''));
$author_bio = trim($_POST['author_bio'] ?? ($logged_user['bio'] ?? ''));
$category = trim($_POST['category'] ?? 'सामान्य');
$excerpt = normalize_content_text($_POST['excerpt'] ?? '');
$content = normalize_content_text($_POST['content'] ?? '');
$author_photo = $logged_user['profile_photo'] ?? 'images/authors/author-default.jpg';

// Determine default cover image fallback
if ($content_type === 'poem') $default_banner = 'images/featured-1.jpg';
elseif ($content_type === 'story') $default_banner = 'images/story-default.jpg';
elseif ($content_type === 'play') $default_banner = 'images/play-default.jpg';
else $default_banner = 'images/article-default.jpg';

// Handle cover image from file upload or URL/Google Drive
$image_url = process_image_input('image_file', 'image_url', $default_banner, 'submissions');

// Handle author photo from file upload or URL/Google Drive if not logged in
if (empty($logged_user)) {
    $author_photo = process_image_input('author_photo_file', 'author_photo_url', $author_photo, 'authors');
}

// Validation
if (empty($title)) {
    send_json_response(false, 'कृपया रचना का शीर्षक अवश्य दर्ज करें।', [], 400);
}
if (empty($author_name)) {
    send_json_response(false, 'कृपया रचनाकार का नाम अवश्य दर्ज करें।', [], 400);
}
if (empty($author_email) || !filter_var($author_email, FILTER_VALIDATE_EMAIL)) {
    send_json_response(false, 'कृपया एक मान्य ईमेल पता दर्ज करें।', [], 400);
}
if (empty($content)) {
    send_json_response(false, 'कृपया मुख्य रचना सामग्री / पाठ अवश्य लिखें।', [], 400);
}

// If excerpt is empty, generate from content
if (empty($excerpt)) {
    $excerpt = make_excerpt($content, 140);
}

// Set default fallback cover image if none provided
if (empty($image_url)) {
    if ($content_type === 'poem') $image_url = 'images/featured-1.jpg';
    elseif ($content_type === 'story') $image_url = 'images/story-default.jpg';
    elseif ($content_type === 'play') $image_url = 'images/play-default.jpg';
    else $image_url = 'images/article-default.jpg';
}

// Insert into user_submissions
$stmt = $conn->prepare("
    INSERT INTO user_submissions (
        user_id, author_name, author_email, author_bio, author_photo, 
        content_type, title, category, excerpt, content, image_url, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");

if (!$stmt) {
    send_json_response(false, 'डेटाबेस क्वेरी तैयार करने में त्रुटि: ' . $conn->error, [], 500);
}

$stmt->bind_param(
    "issssssssss",
    $user_id,
    $author_name,
    $author_email,
    $author_bio,
    $author_photo,
    $content_type,
    $title,
    $category,
    $excerpt,
    $content,
    $image_url
);

if ($stmt->execute()) {
    $sub_id = $stmt->insert_id;
    $stmt->close();
    send_json_response(true, 'आपकी रचना सफलतापूर्वक व्यवस्थापक (Admin) को भेज दी गई है! समीक्षा के उपरांत इसे प्रकाशित कर दिया जाएगा।', [
        'submission_id' => $sub_id,
        'title' => $title,
        'author_name' => $author_name
    ]);
} else {
    $err = $stmt->error;
    $stmt->close();
    send_json_response(false, 'रचना सबमिट करने में त्रुटि आई: ' . $err, [], 500);
}
?>
