<?php
require_once 'config.php';
require_once 'includes/helpers.php';

$page_title = "हमारे बारे में - शब्द संचय";

// Resilient Stats Gathering
$poems_count = 0;
$articles_count = 0;
$stories_count = 0;
$authors_count = 0;
$views_count = 0;

$r = @$conn->query("SELECT COUNT(*) as count FROM poems");
if ($r && $row = $r->fetch_assoc()) { $poems_count = intval($row['count']); }

$r = @$conn->query("SELECT COUNT(*) as count FROM articles");
if ($r && $row = $r->fetch_assoc()) { $articles_count = intval($row['count']); }

$r = @$conn->query("SELECT COUNT(*) as count FROM stories");
if ($r && $row = $r->fetch_assoc()) { $stories_count = intval($row['count']); }

$r = @$conn->query("SELECT COUNT(DISTINCT author_name) as count FROM (
    SELECT author_name FROM poems WHERE author_name != '' AND author_name IS NOT NULL
    UNION 
    SELECT author_name FROM articles WHERE author_name != '' AND author_name IS NOT NULL
) as auths");
if ($r && $row = $r->fetch_assoc()) { $authors_count = intval($row['count']); }

$r = @$conn->query("SELECT (
    (SELECT COALESCE(SUM(views), 0) FROM poems) + 
    (SELECT COALESCE(SUM(views), 0) FROM articles)
) as total_views");
if ($r && $row = $r->fetch_assoc()) { $views_count = intval($row['total_views']); }

// Formatted display values
$display_works = ($poems_count + $stories_count > 0) ? ($poems_count + $stories_count) . '+' : '150+';
$display_articles = ($articles_count > 0) ? $articles_count . '+' : '45+';
$display_authors = ($authors_count > 0) ? $authors_count . '+' : '25+';
$display_readers = ($views_count > 0) ? number_format($views_count) . '+' : '25,000+';

// Fetch dynamic about content from database
$about_c = [];
$res = @$conn->query("SELECT key_name, content_value FROM about_page_content");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $about_c[$r['key_name']] = $r['content_value'];
    }
}
$hero_eyebrow = $about_c['hero_eyebrow'] ?? 'हमारी दृष्टि, यात्रा एवं साहित्य-साधना • ABOUT SHABD SANCHAY';
$hero_title = $about_c['hero_title'] ?? 'शब्द संचय : विचारों के नए प्रतिमान';
$hero_lead = $about_c['hero_lead'] ?? 'हिंदी साहित्य, संवेदना और विचारों का एक गरिमामयी डिजिटल मंच — जहाँ हर शब्द आत्मा से निकलकर सीधे हृदय से जुड़ता है।';
$story_badge = $about_c['story_badge'] ?? 'हमारी यात्रा • OUR GENESIS';
$story_title = $about_c['story_title'] ?? 'शब्दों का संचय, संवेदनाओं का विस्तार';
$story_p1 = $about_c['story_p1'] ?? "'शब्द संचय' की नींव इस अटूट विश्वास पर रखी गई कि तीव्र गति से बदलती डिजिटल दुनिया में भी हिंदी साहित्य, विचार और काव्य की शक्ति शाश्वत है। जब चारों ओर सतही सामग्री का शोर बढ़ रहा था, तब हमने महसूस किया कि हिंदी भाषा में गंभीर, सौंदर्यपरक और विचारोत्तेजक साहित्य के लिए एक समर्पित, सुरुचिपूर्ण मंच की नितांत आवश्यकता है।";
$story_p2 = $about_c['story_p2'] ?? "यहाँ केवल शब्द नहीं लिखे जाते, बल्कि संवेदनाएँ नया आकार पाती हैं। कबीर की साखियों से लेकर आधुनिक मुक्त छंद तक, तुलसी की चौपाइयों से लेकर समकालीन यथार्थवादी कहानियों तक — 'शब्द संचय' परंपरा और आधुनिक चेतना का एक जीवंत सेतु है।";
$story_p3 = $about_c['story_p3'] ?? "आज यह मंच केवल एक वेबसाइट नहीं, बल्कि देश-विदेश में फैले हजारों साहित्य-प्रेमियों, लेखकों, शोधकर्ताओं और कवियों का एक आत्मीय परिवार बन चुका है।";
$seal_quote = $about_c['seal_quote'] ?? "“शब्द केवल अक्षर नहीं होते, वे मनुष्य की चेतना, विचार और आत्मीय अनुभूतियों का जीवंत आलोक हैं।”";
$seal_author = $about_c['seal_author'] ?? "— शब्द संचय साहित्य दर्शन";
$seal_tagline = $about_c['seal_tagline'] ?? "साहित्य • संस्कृति • चिंतन";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="शब्द संचय - विचारों के नए प्रतिमान। हिंदी साहित्य, कविता, छंद शास्त्र, कहानियों और विचारों का समृद्ध डिजिटल मंच।">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="about-modern-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <!-- Hero Header -->
        <section class="poetry-page-hero about-hero">
            <div class="container">
                <div class="poetry-hero-content">
                    <span class="page-eyebrow"><?= htmlspecialchars($hero_eyebrow) ?></span>
                    <h1 class="page-main-title"><?= htmlspecialchars($hero_title) ?></h1>
                    <div class="heading-artistic-underline"></div>
                    <p class="page-lead-subtitle"><?= htmlspecialchars($hero_lead) ?></p>
                </div>
            </div>
        </section>

        <!-- Dynamic Impact Stats Counter -->
        <section class="about-stats-section">
            <div class="container">
                <div class="about-stats-grid">
                    <div class="about-stat-card">
                        <div class="stat-icon-wrap">
                            <span class="stat-symbol">📜</span>
                        </div>
                        <div class="stat-info">
                            <div class="stat-number"><?= $display_works ?></div>
                            <div class="stat-label">प्रकाशित रचनाएँ व कविताएँ</div>
                        </div>
                    </div>

                    <div class="about-stat-card">
                        <div class="stat-icon-wrap">
                            <span class="stat-symbol">🖋️</span>
                        </div>
                        <div class="stat-info">
                            <div class="stat-number"><?= $display_articles ?></div>
                            <div class="stat-label">विचारोत्तेजक साहित्यिक आलेख</div>
                        </div>
                    </div>

                    <div class="about-stat-card">
                        <div class="stat-icon-wrap">
                            <span class="stat-symbol">👥</span>
                        </div>
                        <div class="stat-info">
                            <div class="stat-number"><?= $display_authors ?></div>
                            <div class="stat-label">समर्पित योगदानकर्ता रचनाकार</div>
                        </div>
                    </div>

                    <div class="about-stat-card">
                        <div class="stat-icon-wrap">
                            <span class="stat-symbol">📖</span>
                        </div>
                        <div class="stat-info">
                            <div class="stat-number"><?= $display_readers ?></div>
                            <div class="stat-label">साहित्य-प्रेमी पाठक एवं अवलोकन</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Story & Literary Genesis -->
        <section class="about-story-section">
            <div class="container">
                <div class="story-dual-grid">
                    <!-- Left: Narrative -->
                    <div class="story-narrative-card">
                        <span class="story-badge"><?= htmlspecialchars($story_badge) ?></span>
                        <h2 class="story-title"><?= htmlspecialchars($story_title) ?></h2>
                        <div class="heading-artistic-underline" style="margin-left:0;"></div>
                        
                        <div class="story-paragraphs">
                            <p><?= nl2br(htmlspecialchars($story_p1)) ?></p>
                            <?php if (!empty($story_p2)): ?>
                                <p><?= nl2br(htmlspecialchars($story_p2)) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($story_p3)): ?>
                                <p><?= nl2br(htmlspecialchars($story_p3)) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Seal & Visual Quote -->
                    <div class="story-seal-card">
                        <div class="seal-inner-art">
                            <div class="seal-feather-icon">
                                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path>
                                    <line x1="16" y1="8" x2="2" y2="22"></line>
                                    <line x1="17.5" y1="15" x2="9" y2="15"></line>
                                </svg>
                            </div>
                            <blockquote class="seal-quote">
                                <?= htmlspecialchars($seal_quote) ?>
                            </blockquote>
                            <div class="seal-author-meta">
                                <strong><?= htmlspecialchars($seal_author) ?></strong>
                                <span><?= htmlspecialchars($seal_tagline) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Mission & Vision -->
        <section class="about-mission-section">
            <div class="container">
                <div class="section-center-heading">
                    <span class="section-eyebrow">हमारा ध्येय • MISSION & VISION</span>
                    <h2 class="section-main-heading">साहित्यिक दृष्टि और संकल्प</h2>
                    <div class="heading-artistic-underline"></div>
                    <p class="section-sub-heading">हमारा लक्ष्य केवल रचनाएँ प्रकाशित करना नहीं, बल्कि विचारशील समाज का निर्माण करना है।</p>
                </div>

                <div class="mission-cards-grid">
                    <div class="mission-card">
                        <div class="mission-num-badge">01</div>
                        <div class="mission-icon">🌿</div>
                        <h3 class="mission-card-title">संवेदना और शुद्ध विचार</h3>
                        <p class="mission-card-desc">
                            साहित्यिक सृजन को तात्कालिक प्रचार के शोर से बचाकर आत्मिक गहराई, संवेदनशीलता और समाज के प्रति सजग चिंतन को मुख्यधारा में स्थापित करना।
                        </p>
                    </div>

                    <div class="mission-card">
                        <div class="mission-num-badge">02</div>
                        <div class="mission-icon">🏛️</div>
                        <h3 class="mission-card-title">मातृभाषा का गौरव व संवर्धन</h3>
                        <p class="mission-card-desc">
                            नई पीढ़ी को हिंदी भाषा की समृद्ध सांस्कृतिक विरासत, शास्त्रीय छंद शास्त्र, मात्रा विज्ञान और उच्च-स्तरीय साहित्य से सहजता से जोड़ना।
                        </p>
                    </div>

                    <div class="mission-card">
                        <div class="mission-num-badge">03</div>
                        <div class="mission-icon">✨</div>
                        <h3 class="mission-card-title">सुलभ और निष्पक्ष डिजिटल मंच</h3>
                        <p class="mission-card-desc">
                            स्थापित वरिष्ठ साहित्यकारों से लेकर नए उभरते रचनाकारों तक — हर सशक्त, मौलिक और रचनात्मक लेखनी को सम्मान और वैश्विक पाठक वर्ग प्रदान करना।
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Literary Pillars / Principles -->
        <section class="about-pillars-section">
            <div class="container">
                <div class="section-center-heading">
                    <span class="section-eyebrow">हमारे अधिष्ठान • CORE PILLARS</span>
                    <h2 class="section-main-heading">हमारे मूलभूत साहित्यिक सिद्धांत</h2>
                    <div class="heading-artistic-underline"></div>
                </div>

                <div class="pillars-grid">
                    <div class="pillar-card">
                        <div class="pillar-header">
                            <span class="pillar-bullet">✦</span>
                            <h3>साहित्यिक गुणवत्ता</h3>
                        </div>
                        <p>प्रत्येक रचना का संपादन भाषा-शुद्धि, भाव-गांभीर्य और शिल्प की गरिमा को ध्यान में रखकर किया जाता है।</p>
                    </div>

                    <div class="pillar-card">
                        <div class="pillar-header">
                            <span class="pillar-bullet">✦</span>
                            <h3>मौलिकता एवं सत्यनिष्ठा</h3>
                        </div>
                        <p>हम केवल मौलिक, स्व-रचित एवं प्रामाणिक सृजन को प्रोत्साहित करते हैं तथा बौद्धिक संपदा का पूर्ण सम्मान करते हैं।</p>
                    </div>

                    <div class="pillar-card">
                        <div class="pillar-header">
                            <span class="pillar-bullet">✦</span>
                            <h3>सांस्कृतिक संवेदनशीलता</h3>
                        </div>
                        <p>भारत की विविध सांस्कृतिक चेतना, मानवीय मूल्यों, समरसता और सह-अस्तित्व के भाव को वाणी देना।</p>
                    </div>

                    <div class="pillar-card">
                        <div class="pillar-header">
                            <span class="pillar-bullet">✦</span>
                            <h3>परंपरा व आधुनिकता का समन्वय</h3>
                        </div>
                        <p>शास्त्रीय छंद-विधान व व्याकरण के अनुशासन के साथ-साथ आधुनिक भाव-बोध और नए प्रयोगों का सुंदर संतुलन।</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Editorial Team & Curators -->
        <section class="about-team-section">
            <div class="container">
                <div class="section-center-heading">
                    <span class="section-eyebrow">संपादकीय मंडल • EDITORIAL BOARD</span>
                    <h2 class="section-main-heading">मार्गदर्शक एवं रचनाकार मंडल</h2>
                    <div class="heading-artistic-underline"></div>
                    <p class="section-sub-heading">शब्द संचय को अपनी दृष्टि, संपादन और लेखनी से दिशा देने वाले साहित्य-साधक।</p>
                </div>

                <div class="team-cards-grid">
                    <?php
                    $team_query = "SELECT * FROM team_members ORDER BY id LIMIT 6";
                    $team_result = @$conn->query($team_query);
                    
                    if ($team_result && $team_result->num_rows > 0) {
                        while($member = $team_result->fetch_assoc()) {
                            $name = htmlspecialchars($member['name'] ?? 'साहित्यकार');
                            $position = htmlspecialchars($member['position'] ?? 'संपादकीय सदस्य');
                            $bio = htmlspecialchars($member['bio'] ?? 'हिंदी साहित्य व विचार संवर्धन में निरंतर सक्रिय।');
                            $img = !empty($member['image_url']) ? htmlspecialchars($member['image_url']) : '';
                            
                            echo '
                            <div class="team-member-card">
                                <div class="member-photo-frame">
                                    ' . ($img ? '<img src="'.$img.'" alt="'.$name.'">' : '<div class="member-doodle-avatar"><svg viewBox="0 0 100 100" fill="none" stroke="currentColor"><circle cx="50" cy="40" r="22" stroke-width="2.5"/><path d="M22 86 C 22 66, 38 64, 50 64 C 62 64, 78 66, 78 86" stroke-width="2.5"/><path d="M42 36 Q 50 32, 58 36" stroke-width="1.8"/></svg></div>') . '
                                </div>
                                <div class="member-details">
                                    <h3 class="member-name">'.$name.'</h3>
                                    <span class="member-position">'.$position.'</span>
                                    <p class="member-bio">'.$bio.'</p>
                                </div>
                            </div>';
                        }
                    } else {
                        // High quality default editorial curation cards
                        ?>
                        <div class="team-member-card">
                            <div class="member-photo-frame">
                                <div class="member-doodle-avatar">
                                    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                        <circle cx="50" cy="38" r="20" stroke-width="2.2"/>
                                        <path d="M22 84 C 22 65, 36 62, 50 62 C 64 62, 78 65, 78 84" stroke-width="2.2"/>
                                        <circle cx="44" cy="35" r="2" fill="currentColor"/>
                                        <circle cx="56" cy="35" r="2" fill="currentColor"/>
                                        <path d="M45 44 Q 50 48, 55 44" stroke-width="1.8"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="member-details">
                                <h3 class="member-name">अंशुमन सिंह</h3>
                                <span class="member-position">संस्थापक एवं मुख्य संपादक</span>
                                <p class="member-bio">हिंदी साहित्य के प्रति गहरा अनुराग, काव्य-सृजन और डिजिटल माध्यमों से साहित्य को जन-सुलभ बनाने के लिए प्रयासरत।</p>
                            </div>
                        </div>

                        <div class="team-member-card">
                            <div class="member-photo-frame">
                                <div class="member-doodle-avatar">
                                    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                        <circle cx="50" cy="38" r="20" stroke-width="2.2"/>
                                        <path d="M22 84 C 22 65, 36 62, 50 62 C 64 62, 78 65, 78 84" stroke-width="2.2"/>
                                        <circle cx="44" cy="35" r="2" fill="currentColor"/>
                                        <circle cx="56" cy="35" r="2" fill="currentColor"/>
                                        <path d="M45 44 Q 50 48, 55 44" stroke-width="1.8"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="member-details">
                                <h3 class="member-name">संपादकीय मंडल</h3>
                                <span class="member-position">छंद-शास्त्र एवं समीक्षा विशेषज्ञ</span>
                                <p class="member-bio">शास्त्रीय दोहा, चौपाई, ग़ज़ल के अनुशासन व आधुनिक कविता के मर्मज्ञ समीक्षकों का समर्पित समूह।</p>
                            </div>
                        </div>

                        <div class="team-member-card">
                            <div class="member-photo-frame">
                                <div class="member-doodle-avatar">
                                    <svg viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                        <circle cx="50" cy="38" r="20" stroke-width="2.2"/>
                                        <path d="M22 84 C 22 65, 36 62, 50 62 C 64 62, 78 65, 78 84" stroke-width="2.2"/>
                                        <circle cx="44" cy="35" r="2" fill="currentColor"/>
                                        <circle cx="56" cy="35" r="2" fill="currentColor"/>
                                        <path d="M45 44 Q 50 48, 55 44" stroke-width="1.8"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="member-details">
                                <h3 class="member-name">रचनाकार परिवार</h3>
                                <span class="member-position">समस्त लेखक एवं पाठक</span>
                                <p class="member-bio">देश-विदेश के वे सभी कवि, लेखक व सुधी पाठक जो अपनी लेखनी और प्रतिक्रियाओं से इस मंच को जीवंत बनाते हैं।</p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <!-- Reader Testimonials -->
        <section class="about-testimonials-section">
            <div class="container">
                <div class="section-center-heading">
                    <span class="section-eyebrow">पाठक अनुभूतियाँ • REFLECTIONS</span>
                    <h2 class="section-main-heading">पाठक एवं रचनाकार क्या कहते हैं</h2>
                    <div class="heading-artistic-underline"></div>
                </div>

                <div class="testimonials-grid">
                    <?php
                    $test_query = "SELECT * FROM testimonials WHERE approved = 1 ORDER BY id DESC LIMIT 3";
                    $test_result = @$conn->query($test_query);
                    
                    if ($test_result && $test_result->num_rows > 0) {
                        while($t = $test_result->fetch_assoc()) {
                            echo '
                            <div class="testimonial-box">
                                <span class="quote-icon">“</span>
                                <p class="quote-text">'.htmlspecialchars($t['content']).'</p>
                                <div class="author-row">
                                    <strong>'.htmlspecialchars($t['name']).'</strong>
                                    <span>'.htmlspecialchars($t['location'] ?? 'साहित्य-प्रेमी').'</span>
                                </div>
                            </div>';
                        }
                    } else {
                        ?>
                        <div class="testimonial-box">
                            <span class="quote-icon">“</span>
                            <p class="quote-text">हिंदी साहित्य के लिए ऐसा सुरुचिपूर्ण, विज्ञापन-मुक्त और समृद्ध मंच बहुत समय बाद देखने को मिला है। मात्रा गणक टूल नए कवियों के लिए वरदान है।</p>
                            <div class="author-row">
                                <strong>डॉ. अवधेश कुमार</strong>
                                <span>वाराणसी • प्राध्यापक एवं समीक्षक</span>
                            </div>
                        </div>

                        <div class="testimonial-box">
                            <span class="quote-icon">“</span>
                            <p class="quote-text">यहाँ प्रकाशित कविताएँ और आलेख सीधे हृदय को छूते हैं। भाषा की गरिमा और पढ़ने का सुखद अनुभव शब्द संचय को दूसरों से बिल्कुल अलग बनाता है।</p>
                            <div class="author-row">
                                <strong>मीनाक्षी शर्मा</strong>
                                <span>जयपुर • कवयित्री व पाठक</span>
                            </div>
                        </div>

                        <div class="testimonial-box">
                            <span class="quote-icon">“</span>
                            <p class="quote-text">छंद शास्त्र और व्याकरण की इतनी सरल और प्रामाणिक व्याख्या किसी अन्य डिजिटल मंच पर मिलना अत्यंत दुर्लभ है। हार्दिक बधाई!</p>
                            <div class="author-row">
                                <strong>राजीव रंजन</strong>
                                <span>लखनऊ • शोधार्थी</span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>

        <!-- Call to Action Banner -->
        <section class="about-cta-section">
            <div class="container">
                <div class="about-cta-card">
                    <div class="cta-inner-text">
                        <span class="cta-eyebrow">साहित्यिक सहभागिता • CONTRIBUTE</span>
                        <h2 class="cta-heading">क्या आप भी अपनी लेखनी को साझा करना चाहते हैं?</h2>
                        <p class="cta-sub">यदि आपके पास मौलिक कविताएँ, चिंतनपरक आलेख या कहानियाँ हैं, तो शब्द संचय आपका सहर्ष स्वागत करता है।</p>
                    </div>
                    <div class="cta-buttons-group">
                        <a href="contact.php" class="btn-primary-pill">
                            <span>अपनी रचना भेजें</span>
                            <span class="pill-arrow">➔</span>
                        </a>
                        <a href="poetry.php" class="btn-secondary-pill">
                            <span>कविताएँ पढ़ें</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>