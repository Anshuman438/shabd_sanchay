<?php
// database/setup_submissions_and_users.php
require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

// 1. Create `users` table for member/author accounts
$sql_users = "
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `bio` TEXT NULL,
    `profile_photo` VARCHAR(255) DEFAULT 'images/authors/author-default.jpg',
    `phone` VARCHAR(30) NULL,
    `role` VARCHAR(50) DEFAULT 'author',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_users)) {
    echo "SUCCESS: `users` table created/verified.\n";
} else {
    echo "ERROR users: " . $conn->error . "\n";
}

// 2. Create `user_submissions` table
$sql_submissions = "
CREATE TABLE IF NOT EXISTS `user_submissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `author_name` VARCHAR(150) NOT NULL,
    `author_email` VARCHAR(150) NOT NULL,
    `author_bio` TEXT NULL,
    `author_photo` VARCHAR(255) NULL,
    `content_type` ENUM('poem', 'article', 'story', 'play') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) DEFAULT 'सामान्य',
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `image_url` VARCHAR(255) NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `admin_note` TEXT NULL,
    `published_content_id` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `reviewed_at` TIMESTAMP NULL,
    INDEX (`status`),
    INDEX (`content_type`),
    INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($sql_submissions)) {
    echo "SUCCESS: `user_submissions` table created/verified.\n";
} else {
    echo "ERROR user_submissions: " . $conn->error . "\n";
}
?>
