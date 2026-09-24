<?php
// database/migrate_v2.php
require_once __DIR__ . '/../config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html lang='hi'>
<head>
    <meta charset='UTF-8'>
    <title>शब्द संचय - Database Upgrade v2</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f4f8; padding: 30px 20px; }
        .card { background: white; max-width: 750px; margin: 0 auto; padding: 25px 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        h1 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; }
        .log-item { padding: 10px 14px; margin: 8px 0; border-radius: 6px; font-size: 15px; }
        .success { background: #dcfce7; color: #166534; border-left: 4px solid #22c55e; }
        .info { background: #e0f2fe; color: #075985; border-left: 4px solid #0284c7; }
        .error { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>
<div class='card'>
    <h1>🌸 शब्द संचय (Shabd Sanchay) - Database Migration v2</h1>
";

$logs = [];

// 1. Create admin_users table
$admin_table_sql = "
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(150) DEFAULT 'प्रशासक',
    `role` VARCHAR(50) DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($admin_table_sql)) {
    echo "<div class='log-item success'>✓ `admin_users` तालिका सफलतापूर्वक सत्यापित/निर्मित हुई।</div>";
} else {
    echo "<div class='log-item error'>✗ `admin_users` तालिका त्रुटि: " . $conn->error . "</div>";
}

// Check if default admin exists
$admin_check = $conn->query("SELECT id FROM `admin_users` WHERE `username` = 'admin'");
if ($admin_check && $admin_check->num_rows === 0) {
    $default_hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO `admin_users` (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)");
    $u = 'admin';
    $e = 'admin@hindisahitya.com';
    $name = 'मुख्य प्रशासक';
    $role = 'superadmin';
    $stmt->bind_param("sssss", $u, $e, $default_hash, $name, $role);
    if ($stmt->execute()) {
        echo "<div class='log-item success'>✓ डिफ़ॉल्ट व्यवस्थापक (Admin) खाता निर्मित: उपयोगकर्ता नाम: <strong>admin</strong> | पासवर्ड: <strong>admin123</strong></div>";
    }
    $stmt->close();
} else {
    echo "<div class='log-item info'>ℹ व्यवस्थापक खाता पहले से मौजूद है।</div>";
}

// 2. Create stories table (कहानियाँ)
$stories_table_sql = "
CREATE TABLE IF NOT EXISTS `stories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `excerpt` TEXT,
    `content` LONGTEXT NOT NULL,
    `author_name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) DEFAULT 'सामाजिक',
    `read_time` INT DEFAULT 7,
    `image_url` VARCHAR(255) DEFAULT 'images/story-default.jpg',
    `views` INT DEFAULT 0,
    `likes` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($stories_table_sql)) {
    echo "<div class='log-item success'>✓ `stories` (कहानियाँ) तालिका निर्मित/सत्यापित हुई।</div>";
} else {
    echo "<div class='log-item error'>✗ `stories` त्रुटि: " . $conn->error . "</div>";
}

// 3. Create plays table (नाटक)
$plays_table_sql = "
CREATE TABLE IF NOT EXISTS `plays` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `excerpt` TEXT,
    `content` LONGTEXT NOT NULL,
    `author_name` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) DEFAULT 'ऐतिहासिक',
    `acts_count` INT DEFAULT 3,
    `image_url` VARCHAR(255) DEFAULT 'images/play-default.jpg',
    `views` INT DEFAULT 0,
    `likes` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if ($conn->query($plays_table_sql)) {
    echo "<div class='log-item success'>✓ `plays` (नाटक) तालिका निर्मित/सत्यापित हुई।</div>";
} else {
    echo "<div class='log-item error'>✗ `plays` त्रुटि: " . $conn->error . "</div>";
}

// 4. Update comments table schema
@$conn->query("ALTER TABLE `comments` MODIFY COLUMN `content_type` ENUM('poem', 'article', 'story', 'play') NOT NULL DEFAULT 'poem'");
$check_status_col = $conn->query("SHOW COLUMNS FROM `comments` LIKE 'status'");
if ($check_status_col && $check_status_col->num_rows === 0) {
    if ($conn->query("ALTER TABLE `comments` ADD COLUMN `status` ENUM('approved', 'pending', 'rejected') NOT NULL DEFAULT 'approved' AFTER `comment`")) {
        echo "<div class='log-item success'>✓ `comments` तालिका में `status` कॉलम (टिप्पणी मॉडरेटर) जोड़ा गया।</div>";
    }
} else {
    echo "<div class='log-item info'>ℹ `comments` तालिका में `status` कॉलम पहले से मौजूद है।</div>";
}

// 5. Seed sample stories if empty
$stories_cnt = $conn->query("SELECT COUNT(*) as cnt FROM stories")->fetch_assoc()['cnt'];
if ($stories_cnt == 0) {
    $sample_stories = [
        [
            'ईदगाह',
            'प्रेमचंद की कालजयी कहानी जो बाल मनोविज्ञान और दादी-पोते के असीम स्नेह को जीवंत करती है।',
            "रमजान के पूरे तीस रोजों के बाद आज ईद आई है। कितना मनोहर, कितना सुहावना प्रभाव है! वृक्षों पर कुछ अजीब हरियाली है, खेतों में कुछ अजीब रौनक है, आसमान पर कुछ अजीब लालिमा है। आज का सूर्य देखो, कितना प्यारा, कितना शीतल है, मानों संसार को ईद की बधाई दे रहा है। गाँव में कितनी हलचल है! मेले जाने की तैयारियाँ हो रही हैं।\n\nहामिद चार-पाँच साल का गरीब-सूरत, दुबला-पतला लड़का, जिसका बाप गत वर्ष हैजे की भेंट हो गया और माँ न जाने क्यों पीली होती-होती एक दिन मर गई। अब हामिद अपनी बूढ़ी दादी अमीना की गोद में सोता है और उतना ही प्रसन्न है।\n\nमेले में सभी बच्चे खिलौने और मिठाइयाँ लेते हैं, पर हामिद तीन पैसों से अपनी दादी के लिए चिमटा खरीदता है क्योंकि रोटी सेंकते समय दादी की उँगलियाँ जल जाती थीं। बाल-हृदय के इस त्याग और विवेक ने हर पाठक को भावुक कर दिया।",
            'मुंशी प्रेमचंद',
            'संवेदना',
            8
        ],
        [
            'उसने कहा था',
            'चंद्रधर शर्मा गुलेरी की अमर कहानी जो कर्तव्य, प्रेम और बलिदान की अनूठी मिसाल है।',
            "बड़े-बड़े शहरों के इक्के-गाड़ी वालों की जबान के कोड़ों से जिनकी पीठ छिल गई है और कान पक गए हैं, उनसे हमारी प्रार्थना है कि वे अमृतसर के बम्बूकार्ट वालों की बोली का मरहम लगावें।\n\nलहना सिंह और सूबेदारनी का वह निश्छल बाल-प्रेम युद्ध के मोर्चे पर कर्तव्य की पराकाष्ठा बन गया। जब सूबेदारनी ने अपनी झोली फैलाकर अपने पति और पुत्र की रक्षा की भीख माँगी, तो लहना सिंह ने अपने प्राण देकर उस वादे को पूरा किया। जाते-जाते उसके अंतिम शब्द थे—'उसने कहा था'...",
            'चंद्रधर शर्मा गुलेरी',
            'प्रेम एवं बलिदान',
            10
        ]
    ];

    $stmt = $conn->prepare("INSERT INTO stories (title, excerpt, content, author_name, category, read_time) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($sample_stories as $st) {
        $stmt->bind_param("sssssi", $st[0], $st[1], $st[2], $st[3], $st[4], $st[5]);
        $stmt->execute();
    }
    $stmt->close();
    echo "<div class='log-item success'>✓ कहानियों (Stories) का आरंभिक साहित्यिक डेटा जोड़ा गया।</div>";
}

// 6. Seed sample plays if empty
$plays_cnt = $conn->query("SELECT COUNT(*) as cnt FROM plays")->fetch_assoc()['cnt'];
if ($plays_cnt == 0) {
    $sample_plays = [
        [
            'आषाढ़ का एक दिन',
            'मोहन राकेश द्वारा रचित आधुनिक हिंदी नाटक जो कालिदास के अंतर्द्वंद्व और मल्लिका के मौन समर्पण को दर्शाता है।',
            "पात्र: कालिदास, मल्लिका, अम्बिका, विलोम, मातुल, दन्तुल।\n\n[दृश्य 1: पर्वत शिखर पर स्थित प्रकोष्ठ। बाहर वर्षा की रिमझिम ध्वनि। मल्लिका भीगी हुई प्रवेश करती है।]\n\nमल्लिका: (उल्लास से) अम्बिका! देखो, आज आषाढ़ का पहला दिन है और मेघ कैसे घिरकर आए हैं! जैसे आकाश धरती को अपनी बाहों में समेट लेना चाहता हो।\n\nअम्बिका: (गंभीर स्वर में) वर्षा में भीगना तुम्हें अच्छा लगता है मल्लिका, परंतु जीवन की वास्तविकताएं इन बूंदों जैसी शीतल नहीं होतीं। कालिदास उज्जयिनी जा रहे हैं राजकवि बनने, और तुम यहाँ अकेली रह जाओगी।\n\nकालिदास: (घायल हरिण-शावक को गोद में लिए प्रवेश करते हैं) मल्लिका, इस शावक को देखो, इसे बाण लगा है। हम इसे बचाएंगे। राजप्रसाद के वैभव से अधिक मूल्य इस घाटी की इस माटी और तुम्हारी इन आँखों का है...",
            'मोहन राकेश',
            'ऐतिहासिक नाटक',
            3
        ],
        [
            'अंधा युग',
            'धर्मवीर भारती कृत कालजयी गीतिनाट्य जो महाभारत के अवसान पर युद्ध की विभीषिका और मानवीय मूल्यों के विघटन का चित्रण करता है।',
            "पात्र: धृतराष्ट्र, गांधारी, संजय, कृपाचार्य, युयुत्सु, कृष्ण।\n\n[दृश्य: कुरुक्षेत्र की रक्तरंजित भूमि, युद्ध का अठारहवाँ दिन। चारों ओर गिद्ध मंडरा रहे हैं।]\n\nगांधारी: (आक्रोश और अश्रुपूर्ण स्वर में) हे कृष्ण! यदि तुम चाहते तो यह महाविनाश रुक सकता था। तुमने पांडवों का पक्ष लिया, पर मेरे सौ पुत्रों की चिताओं का उत्तरदायी कौन है? मैं तुम्हें शाप देती हूँ कि जिस प्रकार मेरे कुल का नाश हुआ, उसी प्रकार तुम्हारे कुल का भी...\n\nकृष्ण: (शांत और करुणामय स्वर में) हे माता गांधारी! मैं तुम्हारा शाप अंगीकार करता हूँ। प्रभुता का अंत यही है। जहाँ धर्म का ह्रास और अहंकार का विस्तार होता है, वहाँ केवल अंधा युग शेष रहता है।",
            'धर्मवीर भारती',
            'गीतिनाट्य',
            5
        ]
    ];

    $stmt = $conn->prepare("INSERT INTO plays (title, excerpt, content, author_name, category, acts_count) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($sample_plays as $pl) {
        $stmt->bind_param("sssssi", $pl[0], $pl[1], $pl[2], $pl[3], $pl[4], $pl[5]);
        $stmt->execute();
    }
    $stmt->close();
    echo "<div class='log-item success'>✓ नाटकों (Plays) का आरंभिक साहित्यिक डेटा जोड़ा गया।</div>";
}

echo "
    <br>
    <p style='font-size: 16px; font-weight: bold; color: #15803d;'>🎉 डेटाबेस अपग्रेड (v2) सफलतापूर्वक संपन्न हुआ!</p>
    <a href='../index.php' class='btn'>मुख्य पृष्ठ (Home) पर जाएँ</a> &nbsp;
    <a href='../admin/login.php' class='btn' style='background: #475569;'>व्यवस्थापक लॉगिन (Admin Login)</a>
</div>
</body>
</html>";
?>
