<?php
// database/setup_about_page.php
require_once __DIR__ . '/../config.php';

// 1. Create about_page_content table for custom narrative & mission
$sql_about = "CREATE TABLE IF NOT EXISTS `about_page_content` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) UNIQUE NOT NULL,
  `content_value` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$conn->query($sql_about);

// 2. Ensure team_members table exists and has proper columns
$sql_team = "CREATE TABLE IF NOT EXISTS `team_members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `position` VARCHAR(100) NOT NULL,
  `bio` TEXT,
  `image_url` VARCHAR(255) DEFAULT '',
  `order_index` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$conn->query($sql_team);

// Check if role column exists in team_members and migrate to position if needed
$chk_col = $conn->query("SHOW COLUMNS FROM `team_members` LIKE 'position'");
if ($chk_col && $chk_col->num_rows == 0) {
    $conn->query("ALTER TABLE `team_members` ADD `position` VARCHAR(100) NOT NULL AFTER `name`");
}

// 3. Ensure testimonials table exists
$sql_test = "CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `location` VARCHAR(150) DEFAULT 'साहित्य-प्रेमी',
  `content` TEXT NOT NULL,
  `approved` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
$conn->query($sql_test);

// 4. Populate default about_page_content if empty
$defaults = [
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

foreach ($defaults as $k => $v) {
    $stmt = $conn->prepare("INSERT INTO `about_page_content` (`key_name`, `content_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `key_name` = `key_name`");
    $stmt->bind_param("ss", $k, $v);
    $stmt->execute();
    $stmt->close();
}

// 5. Populate initial team members if table is empty
$t_res = $conn->query("SELECT COUNT(*) as c FROM team_members");
if ($t_res && $t_res->fetch_assoc()['c'] == 0) {
    $members = [
        ['अंशुमन सिंह', 'संस्थापक एवं मुख्य संपादक', 'हिंदी साहित्य के प्रति गहरा अनुराग, काव्य-सृजन और डिजिटल माध्यमों से साहित्य को जन-सुलभ बनाने के लिए प्रयासरत।', 'images/authors/author-default.jpg'],
        ['संपादकीय मंडल', 'छंद-शास्त्र एवं समीक्षा विशेषज्ञ', 'शास्त्रीय दोहा, चौपाई, ग़ज़ल के अनुशासन व आधुनिक कविता के मर्मज्ञ समीक्षकों का समर्पित समूह।', 'images/authors/author-default.jpg'],
        ['रचनाकार परिवार', 'समस्त लेखक एवं पाठक', 'देश-विदेश के वे सभी कवि, लेखक व सुधी पाठक जो अपनी लेखनी और प्रतिक्रियाओं से इस मंच को जीवंत बनाते हैं।', 'images/authors/author-default.jpg']
    ];
    foreach ($members as $m) {
        $ins = $conn->prepare("INSERT INTO team_members (name, position, bio, image_url) VALUES (?, ?, ?, ?)");
        $ins->bind_param("ssss", $m[0], $m[1], $m[2], $m[3]);
        $ins->execute();
        $ins->close();
    }
}

// 6. Populate initial testimonials if table is empty
$test_res = $conn->query("SELECT COUNT(*) as c FROM testimonials");
if ($test_res && $test_res->fetch_assoc()['c'] == 0) {
    $testimonials = [
        ['डॉ. अवधेश कुमार', 'वाराणसी • प्राध्यापक एवं समीक्षक', 'हिंदी साहित्य के लिए ऐसा सुरुचिपूर्ण, विज्ञापन-मुक्त और समृद्ध मंच बहुत समय बाद देखने को मिला है। मात्रा गणक टूल नए कवियों के लिए वरदान है।'],
        ['मीनाक्षी शर्मा', 'जयपुर • कवयित्री व पाठक', 'यहाँ प्रकाशित कविताएँ और आलेख सीधे हृदय को छूते हैं। भाषा की गरिमा और पढ़ने का सुखद अनुभव शब्द संचय को दूसरों से बिल्कुल अलग बनाता है।'],
        ['राजीव रंजन', 'लखनऊ • शोधार्थी', 'छंद शास्त्र और व्याकरण की इतनी सरल और प्रामाणिक व्याख्या किसी अन्य डिजिटल मंच पर मिलना अत्यंत दुर्लभ है। हार्दिक बधाई!']
    ];
    foreach ($testimonials as $t) {
        $ins = $conn->prepare("INSERT INTO testimonials (name, location, content, approved) VALUES (?, ?, ?, 1)");
        $ins->bind_param("sss", $t[0], $t[1], $t[2]);
        $ins->execute();
        $ins->close();
    }
}

echo "About page setup & tables initialized successfully!" . PHP_EOL;
?>
