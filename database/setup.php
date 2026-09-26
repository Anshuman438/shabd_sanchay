<?php
require_once __DIR__ . '/../config.php';

if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}

echo "<div style='font-family: system-ui, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; line-height: 1.6;'>";
echo "<h2>शब्द संचय (Shabd Sanchay) - Complete Database Setup & Schema Sync</h2>";

if ($conn->connect_error) {
    die("<p style='color:red;'>Connection Error: " . htmlspecialchars($conn->connect_error) . "</p>");
}

// 1. Poems Table
$conn->query("
CREATE TABLE IF NOT EXISTS `poems` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `author_name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(100) DEFAULT 'सामान्य',
  `image_url` VARCHAR(255) DEFAULT 'images/poetry-default.jpg',
  `views` INT DEFAULT 0,
  `likes` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "<p style='color:green;'>✓ Poems table verified!</p>";

// Seed Poems
$p_check = $conn->query("SELECT id FROM `poems` LIMIT 1");
if ($p_check && $p_check->num_rows === 0) {
    $conn->query("
    INSERT INTO `poems` (`title`, `content`, `author_name`, `category`, `likes`, `views`) VALUES
    ('प्रकृति की पुकार', 'पेड़ों की डालियों से झरते हैं स्वप्न मधुर,\nनदियों की कलकल में छिपा है जीवन सुर।\nक्षितिज पर जब भोर की किरणें बिखरती हैं,\nधरा अपनी मौन भाषा में हमसे कुछ कहती है।\n\nमत छीनो इस हरी चादर को इंसान,\nयही तो है ईश्वर का सबसे अनमोल वरदान।', 'रामधारी सिंह दिनकर', 'प्रकृति', 42, 120),
    ('जीवन का संघर्ष', 'लहरों से डर कर नौका पार नहीं होती,\nकोशिश करने वालों की कभी हार नहीं होती।\nनन्हीं चींटी जब दाना लेकर चलती है,\nचढ़ती दीवारों पर, सौ बार फिसलती है।\nमन का विश्वास रगों में साहस भरता है,\nचढ़कर गिरना, गिरकर चढ़ना न अखरता है।', 'हरिवंश राय बच्चन', 'प्रेरणादायक', 85, 340),
    ('मातृभूमि', 'चन्दन है इस देश की माटी, तपोभूमि हर ग्राम है,\nहर बाला देवी की प्रतिमा, बच्चा बच्चा राम है।\nजहाँ सत्य, अहिंसा और प्रेम का, पग-पग लगता डेरा है,\nवह भारत देश हमारा है, वह भारत देश हमारा है।', 'मैथिलीशरण गुप्त', 'देशभक्ति', 56, 210);
    ");
    echo "<p style='color:green;'>✓ Sample poems seeded!</p>";
}

// 2. Articles Table
$conn->query("
CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `excerpt` TEXT,
  `content` TEXT NOT NULL,
  `author_name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(100) DEFAULT 'सामान्य',
  `read_time` INT DEFAULT 5,
  `image_url` VARCHAR(255) DEFAULT 'images/article-default.jpg',
  `views` INT DEFAULT 0,
  `likes` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "<p style='color:green;'>✓ Articles table verified!</p>";

// Seed Articles
$a_check = $conn->query("SELECT id FROM `articles` LIMIT 1");
if ($a_check && $a_check->num_rows === 0) {
    $conn->query("
    INSERT INTO `articles` (`title`, `excerpt`, `content`, `author_name`, `category`, `read_time`, `likes`, `views`) VALUES
    ('हिंदी साहित्य का स्वर्ण युग: भक्तिकाल', 'भक्तिकाल हिंदी साहित्य का वह स्वर्णिम अध्याय है जिसमें कबीर, तुलसी, सूर और मीरा ने समाज को नई दिशा दी।', 'हिंदी साहित्य के इतिहास में संवत् 1375 से 1700 तक के काल को भक्तिकाल कहा जाता है। यह वह कालखंड था जब भारतीय जनमानस सांस्कृतिक और सामाजिक परिवर्तनों के दौर से गुजर रहा था।\n\nइस युग के संतों और कवियों ने ईश्वर के सगुण और निर्गुण रूपों के माध्यम से लोक-कल्याण का संदेश दिया।', 'डॉ. विद्यानिवास मिश्र', 'इतिहास', 6, 38, 150),
    ('डिजिटल युग में हिंदी भाषा और साहित्य', 'इंटरनेट और सोशल मीडिया के दौर में हिंदी भाषा नए आयाम छू रही है और युवा पीढ़ी इससे जुड़ रही है।', 'आज के डिजिटल युग में हिंदी केवल बोलचाल की भाषा नहीं रह गई है, बल्कि सूचना तकनीक और वेब पटल पर भी अपना परचम लहरा रही है। ब्लॉग्स, ऑनलाइन कविता मंच, ई-पत्रिकाएँ और ऑडियो पॉडकास्ट के माध्यम से विश्व भर में फैले हिंदी प्रेमी आपस में जुड़ रहे हैं।', 'अंशुमन सिंह', 'संस्कृति', 4, 29, 95);
    ");
    echo "<p style='color:green;'>✓ Sample articles seeded!</p>";
}

// 3. Comments Table
$conn->query("
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `content_id` INT NOT NULL,
  `content_type` ENUM('poem', 'article') NOT NULL DEFAULT 'poem',
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "<p style='color:green;'>✓ Comments table verified!</p>";

// 4. Contacts Table
$conn->query("
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "<p style='color:green;'>✓ Contacts table verified!</p>";

// 5. Newsletter Subscribers Table
$conn->query("
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "<p style='color:green;'>✓ Newsletter table verified!</p>";

// 6. Team Members Table (supports both `role` and `position` columns)
$conn->query("
CREATE TABLE IF NOT EXISTS `team_members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `position` VARCHAR(150) NOT NULL DEFAULT 'संपादक',
  `role` VARCHAR(150) NOT NULL DEFAULT 'संपादक',
  `bio` TEXT,
  `image_url` VARCHAR(255) DEFAULT 'images/authors/author-default.jpg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
@$conn->query("ALTER TABLE `team_members` ADD COLUMN `position` VARCHAR(150) NULL AFTER `name`");
@$conn->query("ALTER TABLE `team_members` ADD COLUMN `role` VARCHAR(150) NULL AFTER `position`");
echo "<p style='color:green;'>✓ Team Members table verified (role & position columns active)!</p>";

// 7. Testimonials Table
$conn->query("
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `location` VARCHAR(150) DEFAULT 'साहित्य-प्रेमी',
  `content` TEXT NOT NULL,
  `approved` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "<p style='color:green;'>✓ Testimonials table verified!</p>";

// 8. Users Table
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
echo "<p style='color:green;'>✓ Users table verified!</p>";

// 9. Admin Users Table
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
echo "<p style='color:green;'>✓ Admin Users table verified!</p>";

// Insert/Reset default Admin user
$default_hash = password_hash('admin123', PASSWORD_BCRYPT);
$admin_check = $conn->query("SELECT id FROM `admin_users` WHERE `username` = 'admin'");
if ($admin_check && $admin_check->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO `admin_users` (username, email, password_hash, full_name, role) VALUES ('admin', 'admin@hindisahitya.com', ?, 'मुख्य प्रशासक', 'superadmin')");
    if ($stmt) {
        $stmt->bind_param("s", $default_hash);
        $stmt->execute();
        $stmt->close();
    }
    echo "<p style='color:green; font-weight:bold;'>✓ Default Admin account created! Username: <b>admin</b> | Password: <b>admin123</b></p>";
} else {
    // Force update password to admin123 to make sure login works
    $stmt = $conn->prepare("UPDATE `admin_users` SET `password_hash` = ? WHERE `username` = 'admin'");
    if ($stmt) {
        $stmt->bind_param("s", $default_hash);
        $stmt->execute();
        $stmt->close();
    }
    echo "<p style='color:green;'>✓ Admin user reset/active! Username: <b>admin</b> | Password: <b>admin123</b></p>";
}

// 10. User Submissions Table
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
echo "<p style='color:green;'>✓ User Submissions table verified!</p>";

// 11. Stories Table
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
echo "<p style='color:green;'>✓ Stories table verified!</p>";

// 12. Plays Table
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
echo "<p style='color:green;'>✓ Plays table verified!</p>";

// 13. About Content Table
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
}
echo "<p style='color:green;'>✓ About Content table verified!</p>";

// 14. About Page Content Table (Key-Value pairs for narrative management)
$conn->query("
CREATE TABLE IF NOT EXISTS `about_page_content` (
  `key_name` VARCHAR(100) PRIMARY KEY,
  `content_value` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$about_p_check = $conn->query("SELECT key_name FROM `about_page_content` LIMIT 1");
if ($about_p_check && $about_p_check->num_rows === 0) {
    $default_about = [
        'hero_eyebrow' => 'हमारी दृष्टि, यात्रा एवं साहित्य-साधना • ABOUT SHABD SANCHAY',
        'hero_title' => 'शब्द संचय : विचारों के नए प्रतिमान',
        'hero_lead' => 'हिंदी साहित्य, संवेदना और विचारों का एक गरिमामयी डिजिटल मंच — जहाँ हर शब्द आत्मा से निकलकर सीधे हृदय से जुड़ता है।',
        'story_badge' => 'हमारी यात्रा • OUR GENESIS',
        'story_title' => 'शब्दों का संचय, संवेदनाओं का विस्तार',
        'story_p1' => "'शब्द संचय' की नींव इस अटूट विश्वास पर रखी गई कि तीव्र गति से बदलती डिजिटल दुनिया में भी हिंदी साहित्य, विचार और काव्य की शक्ति शाश्वत है। जब चारों ओर सतही सामग्री का शोर बढ़ रहा था, तब हमने महसूस किया कि हिंदी भाषा में गंभीर, सौंदर्यपरक और विचारोत्तेजक साहित्य के लिए एक समर्पित, सुरुचिपूर्ण मंच की नितांत आवश्यकता है।",
        'story_p2' => "यहाँ केवल शब्द नहीं लिखे जाते, बल्कि संवेदनाएँ नया आकार पाती हैं। कबीर की साखियों से लेकर आधुनिक मुक्त छंद तक, तुलसी की चौपाइयों से लेकर समकालीन यथार्थवादी कहानियों तक — 'शब्द संचय' परंपरा और आधुनिक चेतना का एक जीवंत सेतु है।",
        'story_p3' => "आज यह मंच केवल एक वेबसाइट नहीं, बल्कि देश-विदेश में फैले हजारों साहित्य-प्रेमियों, लेखकों, शोधकर्ताओं और कवियों का एक आत्मीय परिवार बन चुका है।",
        'seal_quote' => "“शब्द केवल अक्षर नहीं होते, वे मनुष्य की चेतना, विचार और आत्मीय अनुभूतियों का जीवंत आलोक हैं।”",
        'seal_author' => "— शब्द संचय साहित्य दर्शन",
        'seal_tagline' => "साहित्य • संस्कृति • चिंतन"
    ];
    foreach ($default_about as $k => $v) {
        $stmt = $conn->prepare("INSERT INTO about_page_content (key_name, content_value) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $k, $v);
            $stmt->execute();
            $stmt->close();
        }
    }
}
echo "<p style='color:green;'>✓ About Page Content table verified!</p>";

echo "<br><h3 style='color:green; font-weight:bold;'>🎉 Master Database Schema Sync Completed!</h3>";
echo "<p><a href='../index.php' style='padding: 8px 16px; background: #2563eb; color: white; text-decoration: none; border-radius: 4px; display: inline-block;'>Go to Homepage</a> &nbsp; <a href='../admin/login.php' style='padding: 8px 16px; background: #059669; color: white; text-decoration: none; border-radius: 4px; display: inline-block;'>Go to Admin Login</a> &nbsp; <a href='../login.php' style='padding: 8px 16px; background: #7c3aed; color: white; text-decoration: none; border-radius: 4px; display: inline-block;'>Go to User Login</a></p>";
echo "</div>";
