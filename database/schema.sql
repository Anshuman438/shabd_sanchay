-- =========================================================
-- SHABD SANCHAY (शब्द संचय) - Database Schema & Sample Data
-- Charset: utf8mb4 (Full Devanagari & Emoji Support)
-- =========================================================

CREATE DATABASE IF NOT EXISTS `SHABD_SANCHAY` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `SHABD_SANCHAY`;

-- 1. Poems Table (कविताएँ)
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

-- 2. Articles Table (लेख)
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

-- 3. Comments Table (टिप्पणियाँ)
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `content_id` INT NOT NULL,
  `content_type` ENUM('poem', 'article') NOT NULL DEFAULT 'poem',
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Contact Inquiries Table (संपर्क संदेश)
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Newsletter Subscribers Table (समाचार पत्रिका ग्राहक)
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. FAQs Table (अक्सर पूछे जाने वाले प्रश्न)
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NOT NULL,
  `order_index` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Team Members Table (हमारी टीम)
CREATE TABLE IF NOT EXISTS `team_members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(100) NOT NULL,
  `bio` TEXT,
  `image_url` VARCHAR(255) DEFAULT 'images/authors/author-default.jpg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- SEED SAMPLE DATA (आरंभिक साहित्यिक डेटा)
-- =========================================================

-- Sample Poems
INSERT INTO `poems` (`title`, `content`, `author_name`, `category`, `likes`, `views`) VALUES
('प्रकृति की पुकार', 'पेड़ों की डालियों से झरते हैं स्वप्न मधुर,\nनदियों की कलकल में छिपा है जीवन सुर।\nक्षितिज पर जब भोर की किरणें बिखरती हैं,\nधरा अपनी मौन भाषा में हमसे कुछ कहती है।\n\nमत छीनो इस हरी चादर को इंसान,\nयही तो है ईश्वर का सबसे अनमोल वरदान।', 'रामधारी सिंह दिनकर', 'प्रकृति', 42, 120),
('जीवन का संघर्ष', 'लहरों से डर कर नौका पार नहीं होती,\nकोशिश करने वालों की कभी हार नहीं होती।\nनन्हीं चींटी जब दाना लेकर चलती है,\nचढ़ती दीवारों पर, सौ बार फिसलती है।\nमन का विश्वास रगों में साहस भरता है,\nचढ़कर गिरना, गिरकर चढ़ना न अखरता है।', 'हरिवंश राय बच्चन', 'प्रेरणादायक', 85, 340),
('मातृभूमि', 'चन्दन है इस देश की माटी, तपोभूमि हर ग्राम है,\nहर बाला देवी की प्रतिमा, बच्चा बच्चा राम है।\nजहाँ सत्य, अहिंसा और प्रेम का, पग-पग लगता डेरा है,\nवह भारत देश हमारा है, वह भारत देश हमारा है।', 'मैथिलीशरण गुप्त', 'देशभक्ति', 56, 210);

-- Sample Articles
INSERT INTO `articles` (`title`, `excerpt`, `content`, `author_name`, `category`, `read_time`, `likes`, `views`) VALUES
('हिंदी साहित्य का स्वर्ण युग: भक्तिकाल', 'भक्तिकाल हिंदी साहित्य का वह स्वर्णिम अध्याय है जिसमें कबीर, तुलसी, सूर और मीरा ने समाज को नई दिशा दी।', 'हिंदी साहित्य के इतिहास में संवत् 1375 से 1700 तक के काल को ''भक्तिकाल'' कहा जाता है। यह वह कालखंड था जब भारतीय जनमानस सांस्कृतिक और सामाजिक परिवर्तनों के दौर से गुजर रहा था।\n\nइस युग के संतों और कवियों ने ईश्वर के सगुण और निर्गुण रूपों के माध्यम से लोक-कल्याण का संदेश दिया। कबीरदास ने जहाँ सामाजिक आडंबरों पर कुठाराघात किया, वहीं गोस्वामी तुलसीदास जी ने मर्यादा पुरुषोत्तम श्रीराम के पावन चरित्र के जरिए आदर्श समाज की परिकल्पना रखी। सूरदास जी के वात्सल्य रस और मीराबाई की भक्ति ने साहित्य को अमर बना दिया।', 'डॉ. विद्यानिवास मिश्र', 'इतिहास', 6, 38, 150),
('डिजिटल युग में हिंदी भाषा और साहित्य', 'इंटरनेट और सोशल मीडिया के दौर में हिंदी भाषा नए आयाम छू रही है और युवा पीढ़ी इससे जुड़ रही है।', 'आज के डिजिटल युग में हिंदी केवल बोलचाल की भाषा नहीं रह गई है, बल्कि सूचना तकनीक और वेब पटल पर भी अपना परचम लहरा रही है। ब्लॉग्स, ऑनलाइन कविता मंच, ई-पत्रिकाएँ और ऑडियो पॉडकास्ट के माध्यम से विश्व भर में फैले हिंदी प्रेमी आपस में जुड़ रहे हैं।\n\nशब्द संचय जैसे मंच युवाओं को अपनी मौलिक रचनाएँ साझा करने और उत्कृष्ट साहित्य से जुड़ने का आधुनिक माध्यम प्रदान कर रहे हैं।', 'अंशुमन सिंह', 'संस्कृति', 4, 29, 95);

-- Sample Comments
INSERT INTO `comments` (`content_id`, `content_type`, `name`, `email`, `comment`) VALUES
(1, 'poem', 'अमित शर्मा', 'amit@example.com', 'अति सुंदर और हृदयस्पर्शी रचना!'),
(2, 'poem', 'प्रिया वर्मा', 'priya@example.com', 'हरिवंश राय बच्चन जी की यह पंक्तियाँ सदैव नई ऊर्जा देती हैं।'),
(1, 'article', 'संजय गुप्ता', 'sanjay@example.com', 'भक्तिकाल पर अत्यंत शोधपरक और विचारणीय लेख।');
