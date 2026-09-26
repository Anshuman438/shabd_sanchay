<?php
require_once __DIR__ . '/../config.php';

if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}

echo "<h2>शब्द संचय (Shabd Sanchay) - Database Master Setup</h2>";

// 1. Run Base Schema
$sqlFile = __DIR__ . '/schema.sql';
if (file_exists($sqlFile)) {
    $sqlContent = file_get_contents($sqlFile);
    if ($conn->multi_query($sqlContent)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        echo "<p style='color:green;'>✓ Base schema & sample data loaded successfully!</p>";
    } else {
        echo "<p style='color:red;'>Schema error: " . htmlspecialchars($conn->error) . "</p>";
    }
}

// 2. Create `users` table for member registration & login
$conn->query("
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
");
echo "<p style='color:green;'>✓ Users table verified/created!</p>";

// 3. Create `admin_users` table and insert default admin
$conn->query("
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `role` VARCHAR(50) DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$admin_check = $conn->query("SELECT id FROM `admin_users` WHERE `username` = 'admin'");
if ($admin_check && $admin_check->num_rows === 0) {
    $default_hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO `admin_users` (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)");
    $u = 'admin';
    $e = 'admin@hindisahitya.com';
    $fn = 'मुख्य प्रशासक';
    $r = 'superadmin';
    $stmt->bind_param("sssss", $u, $e, $default_hash, $fn, $r);
    $stmt->execute();
    $stmt->close();
    echo "<p style='color:green;'>✓ Default Admin account created (Username: <b>admin</b> | Password: <b>admin123</b>)!</p>";
} else {
    echo "<p style='color:green;'>✓ Admin table verified (Default Admin exists)!</p>";
}

// 4. Create `user_submissions` table
$conn->query("
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
");
echo "<p style='color:green;'>✓ User submissions table verified/created!</p>";

// 5. Create `stories` table
$conn->query("
CREATE TABLE IF NOT EXISTS `stories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `author_name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) DEFAULT 'सामान्य',
    `read_time` INT DEFAULT 5,
    `image_url` VARCHAR(255) DEFAULT 'images/story-default.jpg',
    `views` INT DEFAULT 0,
    `likes` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "<p style='color:green;'>✓ Stories table verified/created!</p>";

// 6. Create `plays` table
$conn->query("
CREATE TABLE IF NOT EXISTS `plays` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `author_name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) DEFAULT 'नाटक',
    `acts_count` INT DEFAULT 1,
    `image_url` VARCHAR(255) DEFAULT 'images/play-default.jpg',
    `views` INT DEFAULT 0,
    `likes` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "<p style='color:green;'>✓ Plays table verified/created!</p>";

// 7. Create `about_content` table
$conn->query("
CREATE TABLE IF NOT EXISTS `about_content` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `hero_title` VARCHAR(255) NOT NULL,
    `hero_subtitle` TEXT NOT NULL,
    `mission_title` VARCHAR(255) NOT NULL,
    `mission_text` TEXT NOT NULL,
    `vision_title` VARCHAR(255) NOT NULL,
    `vision_text` TEXT NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$about_check = $conn->query("SELECT id FROM `about_content` LIMIT 1");
if ($about_check && $about_check->num_rows === 0) {
    $conn->query("
    INSERT INTO `about_content` (hero_title, hero_subtitle, mission_title, mission_text, vision_title, vision_text)
    VALUES (
        'शब्द संचय के बारे में',
        'हिंदी साहित्य, कविता, कहानी और विचारों का एक जीवंत संचय।',
        'हमारा उद्देश्य',
        'हिंदी साहित्य की समृद्धि को नए युग के पाठकों और रचनाकारों तक पहुँचाना।',
        'हमारी दृष्टि',
        'विश्व भर के हिंदी प्रेमियों के लिए एक सशक्त और प्रेरणादायक साहित्यिक मंच का निर्माण करना।'
    );
    ");
    echo "<p style='color:green;'>✓ About Content table initialized with default text!</p>";
} else {
    echo "<p style='color:green;'>✓ About Content table verified!</p>";
}

echo "<br><h3 style='color:green; font-weight:bold;'>🎉 All database tables, users, and admin accounts set up successfully!</h3>";
echo "<p><a href='../index.php' style='padding: 8px 16px; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; display: inline-block;'>Go to Homepage</a> &nbsp; <a href='../admin/login.php' style='padding: 8px 16px; background: #059669; color: white; text-decoration: none; border-radius: 4px; display: inline-block;'>Go to Admin Login</a> &nbsp; <a href='../login.php' style='padding: 8px 16px; background: #7c3aed; color: white; text-decoration: none; border-radius: 4px; display: inline-block;'>Go to User Login</a></p>";
?>
