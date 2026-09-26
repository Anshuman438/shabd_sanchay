<?php
// includes/helpers.php

/**
 * Send a standardized JSON response and exit
 */
function send_json_response($success, $message = '', $data = [], $http_status = 200) {
    if (!headers_sent()) {
        http_response_code($http_status);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode([
        'success' => (bool)$success,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

/**
 * Sanitize string input for safe HTML display
 */
function sanitize_text($input) {
    if (is_null($input)) return '';
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format date in Hindi representation
 */
function format_hindi_date($date_string) {
    if (empty($date_string)) return '';
    $timestamp = strtotime($date_string);
    if (!$timestamp) return $date_string;

    $months = [
        1 => 'जनवरी', 2 => 'फ़रवरी', 3 => 'मार्च', 4 => 'अप्रैल',
        5 => 'मई', 6 => 'जून', 7 => 'जुलाई', 8 => 'अगस्त',
        9 => 'सितंबर', 10 => 'अक्टूबर', 11 => 'नवंबर', 12 => 'दिसंबर'
    ];

    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)] ?? date('M', $timestamp);
    $year = date('Y', $timestamp);

    return "$day $month, $year";
}

/**
 * Estimate reading time in minutes for Hindi/English text
 */
function estimate_reading_time($text, $words_per_minute = 180) {
    $clean_text = strip_tags($text);
    $word_count = count(preg_split('/\s+/u', trim($clean_text), -1, PREG_SPLIT_NO_EMPTY));
    $minutes = ceil($word_count / $words_per_minute);
    return max(1, $minutes);
}

/**
 * Normalize line endings and convert literal escaped '\n', '\r\n' to actual newlines
 */
function normalize_content_text($text) {
    if (!is_string($text)) return '';
    // Convert literal escaped strings \r\n, \n, \r (e.g. from JSON or bad form escaping)
    $text = str_replace(["\\r\\n", "\\n", "\\r"], "\n", $text);
    // Normalize Windows and legacy Mac line endings to standard LF (\n)
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    return trim($text);
}

/**
 * Generate a short excerpt safely from text
 */
function make_excerpt($text, $length = 150) {
    $text = normalize_content_text($text);
    $clean_text = strip_tags($text);
    if (mb_strlen($clean_text, 'UTF-8') <= $length) {
        return $clean_text;
    }
    return mb_substr($clean_text, 0, $length, 'UTF-8') . '...';
}

/**
 * Clean and convert Google Drive share/view URL into a direct embeddable image URL
 */
function clean_google_drive_url($url) {
    if (!is_string($url)) return '';
    $url = trim($url);
    if (empty($url)) return '';

    // Convert Google Drive view/open links
    if (preg_match('/drive\.google\.com\/(?:file\/d\/|open\?id=)([a-zA-Z0-9_-]+)/i', $url, $matches)) {
        $file_id = $matches[1];
        return "https://drive.google.com/uc?export=view&id=" . $file_id;
    }
    return $url;
}

/**
 * Handle image input from either file upload (computer) or direct/Google Drive URL
 */
function process_image_input($file_key, $url_key, $default_fallback = '', $upload_subfolder = '') {
    // 1. Check if a file was uploaded from user's computer
    if (isset($_FILES[$file_key]) && !empty($_FILES[$file_key]['name']) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_key]['tmp_name'];
        $name = $_FILES[$file_key]['name'];
        $size = $_FILES[$file_key]['size'];
        
        if ($size <= 6 * 1024 * 1024) {
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            
            if (in_array($ext, $allowed_exts)) {
                $sub = !empty($upload_subfolder) ? trim($upload_subfolder, '/') . '/' : '';
                $upload_dir = __DIR__ . '/../uploads/' . $sub;
                
                $new_filename = 'img_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $target_path = $upload_dir . $new_filename;
                
                if (!is_dir($upload_dir)) {
                    @mkdir($upload_dir, 0777, true);
                }
                
                if (@move_uploaded_file($tmp_name, $target_path)) {
                    return 'uploads/' . $sub . $new_filename;
                } else {
                    $image_data = @file_get_contents($tmp_name);
                    if ($image_data !== false) {
                        $base64 = base64_encode($image_data);
                        $mime = ($ext === 'svg') ? 'image/svg+xml' : 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext);
                        return 'data:' . $mime . ';base64,' . $base64;
                    }
                }
            }
        }
    }

    // 2. Fallback to provided URL or Google Drive link
    if (isset($_POST[$url_key]) && !empty(trim($_POST[$url_key]))) {
        return clean_google_drive_url($_POST[$url_key]);
    }

    // 3. Fallback to default image URL
    return clean_google_drive_url($default_fallback);
}

/**
 * Single Like Per User / Session Helper
 */
function toggle_user_like($conn, $content_type, $content_id, $action = 'like') {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    
    $user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? null;
    $session_key = $user_id ? 'user_' . $user_id : (session_id() ?: md5($_SERVER['REMOTE_ADDR'] ?? 'guest'));
    
    $table_map = [
        'poem' => 'poems',
        'article' => 'articles',
        'story' => 'stories',
        'play' => 'plays'
    ];
    $target_table = $table_map[$content_type] ?? 'poems';

    // Check if already liked
    $stmt_check = $conn->prepare("SELECT id FROM user_likes WHERE session_key = ? AND content_type = ? AND content_id = ? LIMIT 1");
    if ($stmt_check) {
        $stmt_check->bind_param("ssi", $session_key, $content_type, $content_id);
        $stmt_check->execute();
        $already_liked = $stmt_check->get_result()->num_rows > 0;
        $stmt_check->close();
    } else {
        $already_liked = false;
    }

    if ($action === 'unlike') {
        if ($already_liked) {
            $del = $conn->prepare("DELETE FROM user_likes WHERE session_key = ? AND content_type = ? AND content_id = ?");
            if ($del) {
                $del->bind_param("ssi", $session_key, $content_type, $content_id);
                $del->execute();
                $del->close();
            }

            $upd = $conn->prepare("UPDATE {$target_table} SET likes = GREATEST(0, likes - 1) WHERE id = ?");
            if ($upd) {
                $upd->bind_param("i", $content_id);
                $upd->execute();
                $upd->close();
            }
        }
        $msg = 'पसंद हटा दी गई';
        $is_liked = false;
    } else {
        if (!$already_liked) {
            $ins = $conn->prepare("INSERT INTO user_likes (user_id, session_key, content_type, content_id) VALUES (?, ?, ?, ?)");
            if ($ins) {
                $ins->bind_param("issi", $user_id, $session_key, $content_type, $content_id);
                $ins->execute();
                $ins->close();
            }

            $upd = $conn->prepare("UPDATE {$target_table} SET likes = likes + 1 WHERE id = ?");
            if ($upd) {
                $upd->bind_param("i", $content_id);
                $upd->execute();
                $upd->close();
            }

            $msg = 'सफलतापूर्वक लाइक किया गया';
            $is_liked = true;
        } else {
            $msg = 'आप इसे पहले ही पसंद कर चुके हैं।';
            $is_liked = true;
        }
    }

    // Fetch updated count
    $stmt_cnt = $conn->prepare("SELECT likes FROM {$target_table} WHERE id = ?");
    if ($stmt_cnt) {
        $stmt_cnt->bind_param("i", $content_id);
        $stmt_cnt->execute();
        $res = $stmt_cnt->get_result()->fetch_assoc();
        $newLikes = $res['likes'] ?? 0;
        $stmt_cnt->close();
    } else {
        $newLikes = 0;
    }

    return [
        'newLikes' => intval($newLikes),
        'isLiked' => $is_liked,
        'message' => $msg
    ];
}
?>
