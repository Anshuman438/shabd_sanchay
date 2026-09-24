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
 * Generate a short excerpt safely from text
 */
function make_excerpt($text, $length = 150) {
    $clean_text = strip_tags($text);
    if (mb_strlen($clean_text, 'UTF-8') <= $length) {
        return $clean_text;
    }
    return mb_substr($clean_text, 0, $length, 'UTF-8') . '...';
}
?>
