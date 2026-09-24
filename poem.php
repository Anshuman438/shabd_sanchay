<?php
require_once 'config.php';
require_once 'includes/helpers.php';

if (!isset($_GET['id'])) {
    header("Location: poetry.php");
    exit();
}

$poem_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM poems WHERE id = ?");
$stmt->bind_param("i", $poem_id);
$stmt->execute();
$result = $stmt->get_result();
$poem = $result->fetch_assoc();
$stmt->close();

if (!$poem) {
    header("Location: poetry.php");
    exit();
}

$page_title = htmlspecialchars($poem['title']) . " - कविता | शब्द संचय";

// Increment view count safely
$conn->query("UPDATE poems SET views = views + 1 WHERE id = $poem_id");
$poem['views'] = ($poem['views'] ?? 0) + 1;

// Calculate reading metrics
$content_lines = explode("\n", normalize_content_text($poem['content']));
$stanza_count = 0;
$current_stanza = [];
$stanzas = [];

foreach ($content_lines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '') {
        if (!empty($current_stanza)) {
            $stanzas[] = $current_stanza;
            $current_stanza = [];
        }
    } else {
        $current_stanza[] = $trimmed;
    }
}
if (!empty($current_stanza)) {
    $stanzas[] = $current_stanza;
}
$stanza_count = count($stanzas);

// Author bio lookup / presets for renowned Hindi poets
$poet_bios = [
    'रामधारी सिंह दिनकर' => 'राष्ट्रकवि रामधारी सिंह दिनकर (1908-1974) आधुनिक युग के श्रेष्ठ वीर रस और ओजस्वी चेतना के कवि हैं। उनकी रचनाओं में राष्ट्रीयता, क्रांति, मानवीय संवेदना और सामाजिक चिंतन का अप्रतिम संगम मिलता है।',
    'हरिवंश राय बच्चन' => 'हरिवंश राय बच्चन (1907-2003) हिंदी के प्रसिद्ध कवि और लेखक थे, जिन्हें ‘मधुशाला’ और हालावाद के प्रवर्तक के रूप में जाना जाता है। उनकी कविताएँ जीवन के संघर्ष और आशावादिता से ओत-प्रोत हैं।',
    'मैथिलीशरण गुप्त' => 'राष्ट्रकवि मैथिलीशरण गुप्त (1886-1964) खड़ी बोली हिंदी के प्रथम महत्वपूर्ण कवि माने जाते हैं। उन्होंने ‘साकेत’, ‘भारत-भारती’ और ‘यशोधरा’ जैसी अमर कृतियों से राष्ट्र को जागृत किया।',
    'महादेवी वर्मा' => 'महादेवी वर्मा (1907-1987) छायावाद के चार प्रमुख स्तंभों में से एक हैं। उनकी कविताओं में रहस्यवाद, करुणा, आत्म-निवेदन और सूक्ष्म अनुभूतियों का अत्यंत भावपूर्ण चित्रण मिलता है।',
    'सूर्यकांत त्रिपाठी निराला' => 'सूर्यकांत त्रिपाठी ‘निराला’ (1896-1961) हिंदी साहित्य के अप्रतिम युगांतरकारी कवि हैं, जिन्होंने मुक्त छंद और प्रगतिशील चेतना को नई दिशा और विराट अभिव्यक्ति दी।',
    'जयशंकर प्रसाद' => 'जयशंकर प्रसाद (1889-1937) छायावादी युग के प्रमुख प्रवर्तक एवं ‘कामायनी’ महाकाव्य के रचयिता हैं। उनका साहित्य दार्शनिकता और सौंदर्यबोध का अनुपम संगम है।',
    'सुमित्रानंदन पंत' => 'सुमित्रानंदन पंत (1900-1977) प्रकृति के सुकुमार कवि कहे जाते हैं। उनकी कविताओं में प्रकृति का सजीव, कोमल और दिव्य चित्रण देखने को मिलता है।'
];

$poet_name = trim($poem['author_name']);
$poet_bio = $poet_bios[$poet_name] ?? "हिंदी साहित्य के उत्कृष्ट रचनाकार, जिनकी काव्य-पंक्तियाँ मानवीय भावनाओं, संवेदनाओं और जीवन के विभिन्न रंगों को सुंदर अभिव्यक्ति देती हैं।";

$has_custom_photo = !empty($poem['image_url']) && 
                     $poem['image_url'] !== 'images/poetry-default.jpg' && 
                     !str_contains($poem['image_url'], 'default');
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="<?= htmlspecialchars(mb_substr(str_replace(["\r", "\n"], ' ', $poem['content']), 0, 160)) ?>...">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="poem-single-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="poem-detail-wrapper">
            <div class="container">
                
                <!-- Breadcrumbs -->
                <nav class="poem-breadcrumb" aria-label="ब्रेडक्रम्ब">
                    <a href="index.php">मुख्य पृष्ठ</a>
                    <span class="bc-sep">›</span>
                    <a href="poetry.php">कविताएँ</a>
                    <span class="bc-sep">›</span>
                    <a href="poetry.php?category=<?= urlencode($poem['category'] ?: 'सामान्य') ?>"><?= htmlspecialchars($poem['category'] ?: 'सामान्य') ?></a>
                    <span class="bc-sep">›</span>
                    <span class="bc-current"><?= htmlspecialchars($poem['title']) ?></span>
                </nav>

                <!-- Central Reading Parchment Card -->
                <article class="poem-reading-card">
                    <!-- Top Navigation & Category Pill -->
                    <div class="poem-reading-top-nav">
                        <a href="poetry.php?category=<?= urlencode($poem['category'] ?: 'सामान्य') ?>" class="poem-category-pill">
                            <span class="cat-dot"></span>
                            <span>कविता • <?= htmlspecialchars($poem['category'] ?: 'सामान्य') ?></span>
                        </a>
                        <a href="poetry.php" class="back-to-poems-link">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            <span>सभी कविताएँ</span>
                        </a>
                    </div>

                    <!-- Poem Header Area -->
                    <header class="poem-reading-header">
                        <h1 class="poem-reading-title"><?= htmlspecialchars($poem['title']) ?></h1>
                        <div class="heading-artistic-underline"></div>
                        
                        <div class="poem-reading-meta">
                            <a href="poetry.php?search=<?= urlencode($poem['author_name']) ?>" class="meta-author-tag" title="कवि की रचनाएँ देखें">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span><?= htmlspecialchars($poem['author_name']) ?></span>
                            </a>
                            <span class="meta-separator">•</span>
                            <span class="meta-date-tag">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <span><?= format_hindi_date($poem['created_at']) ?></span>
                            </span>
                            <span class="meta-separator">•</span>
                            <span class="meta-views-tag" title="देखा गया">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span><?= $poem['views'] ?> बार पढ़ा गया</span>
                            </span>
                            <span class="meta-separator">•</span>
                            <span class="meta-stanza-tag">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                                <span><?= $stanza_count ?> छंद / अंतरा</span>
                            </span>
                        </div>
                    </header>

                    <!-- Poem Decorative Quotation Marks and Verses -->
                    <div class="poem-verses-wrapper" id="poem-verses-container">
                        <div class="poem-quote-ornament top-quote">“</div>
                        
                        <div class="poem-stanzas-flow">
                            <?php foreach ($stanzas as $idx => $lines): ?>
                                <div class="poem-stanza-block">
                                    <?php foreach ($lines as $l_idx => $line): 
                                        $unique_line_key = "p{$poem['id']}_s{$idx}_l{$l_idx}";
                                    ?>
                                        <div class="poem-verse-row" id="<?= $unique_line_key ?>" data-line-key="<?= $unique_line_key ?>">
                                            <p class="poem-verse-line"><?= htmlspecialchars($line) ?></p>
                                            <button type="button" class="btn-line-bookmark" onclick="toggleVerseBookmark('<?= $unique_line_key ?>', <?= $poem['id'] ?>, <?= htmlspecialchars(json_encode($poem['title']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($poem['author_name']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($line), ENT_QUOTES, 'UTF-8') ?>, this)" title="पंक्ति सहेजें / बुकमार्क करें">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="poem-quote-ornament bottom-quote">”</div>
                    </div>

                    <!-- Interactive Action Bar (Like, Bookmark, Copy, Font Size, Share) -->
                    <div class="poem-action-toolbar">
                        <div class="action-left-group">
                            <!-- Like Button -->
                            <button type="button" class="poem-like-btn" id="poem-like-button" onclick="likeCurrentPoem(<?= $poem['id'] ?>)" title="कविता पसंद करें">
                                <svg class="like-heart-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                                <span class="like-label">पसंद</span>
                                <span class="like-counter" id="like-count"><?= $poem['likes'] ?></span>
                            </button>

                            <!-- Bookmark Poem Button -->
                            <button type="button" class="poem-bookmark-btn" id="poem-bookmark-button" onclick="toggleWorkBookmark(<?= $poem['id'] ?>, 'poem', <?= htmlspecialchars(json_encode($poem['title']), ENT_QUOTES, 'UTF-8') ?>)" title="कविता सहेजें / बुकमार्क करें">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <span id="poem-bookmark-label">सहेजें</span>
                            </button>

                            <!-- Copy Poem Text Button -->
                            <button type="button" class="poem-copy-btn" id="poem-copy-button" onclick="copyPoemText()" title="पूरी कविता कॉपी करें">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                <span id="copy-btn-text">कविता कॉपी करें</span>
                            </button>
                        </div>

                        <div class="action-right-group">
                            <!-- Font Size Adjuster Controls -->
                            <div class="font-size-adjuster" title="फॉन्ट आकार समायोजित करें">
                                <button type="button" class="font-btn" onclick="adjustPoemFontSize(-1)" title="छोटा फॉन्ट">A−</button>
                                <button type="button" class="font-btn" onclick="resetPoemFontSize()" title="सामान्य फॉन्ट">A</button>
                                <button type="button" class="font-btn" onclick="adjustPoemFontSize(1)" title="बड़ा फॉन्ट">A+</button>
                            </div>

                            <!-- Social Share Buttons -->
                            <div class="poem-social-share-group">
                                <button type="button" class="share-icon-btn whatsapp-share" onclick="shareOnWhatsApp()" title="व्हाट्सएप पर साझा करें" aria-label="WhatsApp">
                                    <svg viewBox="0 0 24 24" width="17" height="17" fill="currentColor"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                                </button>
                                <button type="button" class="share-icon-btn twitter-share" onclick="shareOnTwitter()" title="X (Twitter) पर साझा करें" aria-label="Twitter">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </button>
                                <button type="button" class="share-icon-btn facebook-share" onclick="shareOnFacebook()" title="फेसबुक पर साझा करें" aria-label="Facebook">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                </button>
                                <button type="button" class="share-icon-btn link-share" onclick="copyPoemUrl()" title="लिंक कॉपी करें" aria-label="Copy Link">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Poet Profile Showcase Card (Square Frame with Bottom-Left Double Border) -->
                <section class="poem-author-showcase-card">
                    <div class="author-portrait-column">
                        <div class="card-author-avatar-wrap author-showcase-avatar">
                            <?php if ($has_custom_photo): ?>
                                <img src="<?= htmlspecialchars($poem['image_url']) ?>" alt="<?= htmlspecialchars($poem['author_name']) ?>" class="author-avatar-img" onerror="this.parentElement.innerHTML=getPoetDoodleSVG();">
                            <?php else: ?>
                                <div class="author-doodle-fallback">
                                    <svg class="poet-doodle-svg" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M32 6 C24 6 18 10 18 17 C18 23 23 25 24 30 C20 31 16 34 16 38 C16 43 20 45 22 46 C20 48 18 51 18 56 L46 56 C46 51 44 48 42 46 C44 45 48 43 48 38 C48 34 44 31 40 30 C41 25 46 23 46 17 C46 10 40 6 32 6 Z" stroke-dasharray="1 1" opacity="0.3"/>
                                        <path d="M22 22 C22 14 26 10 32 10 C38 10 42 14 42 22 C42 28 38 33 32 33 C26 33 22 28 22 22 Z"/>
                                        <path d="M20 18 C22 13 26 10 32 10 C39 10 43 14 44 19 C42 16 38 14 32 14 C26 14 22 16 20 18 Z" fill="currentColor" opacity="0.8"/>
                                        <circle cx="28" cy="22" r="1.5" fill="currentColor"/>
                                        <circle cx="36" cy="22" r="1.5" fill="currentColor"/>
                                        <path d="M30 25 C31 27 33 27 34 25" stroke-width="1.4"/>
                                        <path d="M27 28 C29 31 35 31 37 28" stroke-width="1.3"/>
                                        <path d="M19 14 C19 14 23 7 32 7 C41 7 45 14 45 14 C46 11 40 6 32 6 C24 6 19 11 19 14 Z" fill="currentColor" opacity="0.35"/>
                                        <path d="M20 43 C20 37 24 35 32 35 C40 35 44 37 44 43 L48 57 L16 57 Z"/>
                                        <path d="M26 35 L32 44 L38 35"/>
                                        <path d="M42 45 L52 36 L54 38 L44 48 Z" fill="currentColor" opacity="0.3"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="author-details-column">
                        <div class="author-badge-eyebrow">कवि परिचय • POET PROFILE</div>
                        <h2 class="author-showcase-name"><?= htmlspecialchars($poem['author_name']) ?></h2>
                        <p class="author-showcase-bio"><?= htmlspecialchars($poet_bio) ?></p>
                        <div class="author-showcase-footer">
                            <a href="poetry.php?search=<?= urlencode($poem['author_name']) ?>" class="author-explore-btn">
                                <span>इनकी अन्य रचनाएँ पढ़ें</span>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Comments & Discussions Section -->
                <section class="poem-discussion-section" id="comments">
                    <div class="discussion-header-row">
                        <div class="discussion-title-wrap">
                            <h2 class="discussion-title">पाठक प्रतिक्रियाएँ एवं विचार</h2>
                            <div class="heading-artistic-underline" style="margin-left: 0;"></div>
                        </div>
                        <div class="comments-count-badge" id="comments-count-badge">
                            <span>प्रतिक्रियाएँ लोड हो रही हैं...</span>
                        </div>
                    </div>

                    <div class="discussion-grid">
                        <!-- Comment Form Card -->
                        <div class="comment-submission-card">
                            <div class="form-card-header">
                                <h3 class="form-card-title">अपनी टिप्पणी या विचार साझा करें</h3>
                                <p class="form-card-subtitle">साहित्यिक संवाद को समृद्ध करने में अपना योगदान दें।</p>
                            </div>
                            <form id="comment-form" onsubmit="submitComment(event, <?= $poem['id'] ?>)">
                                <div class="form-row-dual">
                                    <div class="custom-form-group">
                                        <label for="comment-name">आपका नाम <span class="required-star">*</span></label>
                                        <input type="text" id="comment-name" class="custom-form-input" placeholder="उदा. राहुल शर्मा" required>
                                    </div>
                                    <div class="custom-form-group">
                                        <label for="comment-email">ईमेल पता <span class="required-star">*</span></label>
                                        <input type="email" id="comment-email" class="custom-form-input" placeholder="उदा. rahul@example.com" required>
                                    </div>
                                </div>
                                <div class="custom-form-group">
                                    <label for="comment-text">आपकी टिप्पणी / समीक्षा <span class="required-star">*</span></label>
                                    <textarea id="comment-text" class="custom-form-textarea" rows="4" placeholder="इस कविता के बारे में अपने विचार यहाँ लिखें..." required></textarea>
                                </div>
                                <button type="submit" class="comment-submit-btn" id="comment-submit-button">
                                    <span>टिप्पणी सबमिट करें</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                </button>
                            </form>
                        </div>

                        <!-- Existing Comments List -->
                        <div class="comments-list-wrap">
                            <h3 class="comments-list-title">हालिया टिप्पणियाँ</h3>
                            <div class="comments-list-container" id="comments-container">
                                <!-- Loaded via JavaScript -->
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Related Poems Showcase (3-Column Grid) -->
                <section class="related-poems-section">
                    <div class="related-section-header">
                        <span class="page-eyebrow">और भी पढ़ें • RELATED POETRY</span>
                        <h2 class="related-section-title">संबंधित उत्कृष्ट रचनाएँ</h2>
                        <div class="heading-artistic-underline"></div>
                        <p class="related-section-subtitle">उसी साहित्यिक रस और भाव में अन्य लोकप्रिय रचनाएँ</p>
                    </div>

                    <div class="poetry-grid" id="related-poems-grid">
                        <!-- Loaded via JavaScript -->
                    </div>
                </section>

            </div>
        </div>
    </main>

    <!-- Toast Notification for Copy -->
    <div id="toast-notify" class="toast-notification" style="display:none;"></div>

    <?php include 'footer.php'; ?>

    <script>
    // Poet Doodle SVG template for JavaScript rendering fallbacks
    function getPoetDoodleSVG() {
        return `
            <div class="author-doodle-fallback">
                <svg class="poet-doodle-svg" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M32 6 C24 6 18 10 18 17 C18 23 23 25 24 30 C20 31 16 34 16 38 C16 43 20 45 22 46 C20 48 18 51 18 56 L46 56 C46 51 44 48 42 46 C44 45 48 43 48 38 C48 34 44 31 40 30 C41 25 46 23 46 17 C46 10 40 6 32 6 Z" stroke-dasharray="1 1" opacity="0.3"/>
                    <path d="M22 22 C22 14 26 10 32 10 C38 10 42 14 42 22 C42 28 38 33 32 33 C26 33 22 28 22 22 Z"/>
                    <path d="M20 18 C22 13 26 10 32 10 C39 10 43 14 44 19 C42 16 38 14 32 14 C26 14 22 16 20 18 Z" fill="currentColor" opacity="0.8"/>
                    <circle cx="28" cy="22" r="1.5" fill="currentColor"/>
                    <circle cx="36" cy="22" r="1.5" fill="currentColor"/>
                    <path d="M30 25 C31 27 33 27 34 25" stroke-width="1.4"/>
                    <path d="M27 28 C29 31 35 31 37 28" stroke-width="1.3"/>
                    <path d="M19 14 C19 14 23 7 32 7 C41 7 45 14 45 14 C46 11 40 6 32 6 C24 6 19 11 19 14 Z" fill="currentColor" opacity="0.35"/>
                    <path d="M20 43 C20 37 24 35 32 35 C40 35 44 37 44 43 L48 57 L16 57 Z"/>
                    <path d="M26 35 L32 44 L38 35"/>
                    <path d="M42 45 L52 36 L54 38 L44 48 Z" fill="currentColor" opacity="0.3"/>
                </svg>
            </div>
        `;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showToast(msg) {
        const toast = document.getElementById('toast-notify');
        if (!toast) return;
        toast.textContent = msg;
        toast.style.display = 'block';
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 2800);
    }

    // Bookmarking Logic
    function getWorkBookmarks() {
        try {
            return JSON.parse(localStorage.getItem('shabd_bookmarks') || '[]');
        } catch(e) {
            return [];
        }
    }

    function getLineBookmarks() {
        try {
            return JSON.parse(localStorage.getItem('shabd_line_bookmarks') || '{}');
        } catch(e) {
            return {};
        }
    }

    function toggleWorkBookmark(id, type, title) {
        const btn = document.getElementById('poem-bookmark-button');
        const label = document.getElementById('poem-bookmark-label');
        let bookmarks = getWorkBookmarks();
        const key = `${type}_${id}`;
        const index = bookmarks.indexOf(key);

        if (index > -1) {
            bookmarks.splice(index, 1);
            if (btn) btn.classList.remove('bookmarked');
            if (label) label.textContent = 'सहेजें';
            showToast('कविता बुकमार्क से हटा दी गई।');
        } else {
            bookmarks.push(key);
            if (btn) btn.classList.add('bookmarked');
            if (label) label.textContent = 'सहेजा गया';
            showToast('कविता सफलतापूर्वक सहेज ली गई!');
        }
        localStorage.setItem('shabd_bookmarks', JSON.stringify(bookmarks));
    }

    function toggleVerseBookmark(lineKey, poemId, poemTitle, poetName, verseText, btnEl) {
        let lineBookmarks = getLineBookmarks();
        const row = document.getElementById(lineKey);

        if (lineBookmarks[lineKey]) {
            delete lineBookmarks[lineKey];
            if (row) row.classList.remove('line-bookmarked');
            showToast('पंक्ति बुकमार्क से हटा दी गई।');
        } else {
            lineBookmarks[lineKey] = {
                poemId: poemId,
                poemTitle: poemTitle,
                author: poetName,
                text: verseText,
                savedAt: new Date().toISOString()
            };
            if (row) row.classList.add('line-bookmarked');
            showToast('पंक्ति सफलतापूर्वक सहेज ली गई!');
        }
        localStorage.setItem('shabd_line_bookmarks', JSON.stringify(lineBookmarks));
    }

    function restoreBookmarksState() {
        const poemId = <?= $poem['id'] ?>;
        // 1. Work bookmark
        const bookmarks = getWorkBookmarks();
        if (bookmarks.includes(`poem_${poemId}`)) {
            const btn = document.getElementById('poem-bookmark-button');
            const label = document.getElementById('poem-bookmark-label');
            if (btn) btn.classList.add('bookmarked');
            if (label) label.textContent = 'सहेजा गया';
        }

        // 2. Line bookmarks
        const lineBookmarks = getLineBookmarks();
        document.querySelectorAll('.poem-verse-row').forEach(row => {
            const key = row.dataset.lineKey;
            if (key && lineBookmarks[key]) {
                row.classList.add('line-bookmarked');
            }
        });
    }

    // Like / Unlike poem functionality
    let hasLiked = localStorage.getItem('poem_liked_' + <?= $poem['id'] ?>) === 'true';
    if (hasLiked) {
        document.getElementById('poem-like-button')?.classList.add('liked');
    }

    async function likeCurrentPoem(poemId) {
        const btn = document.getElementById('poem-like-button');
        const action = hasLiked ? 'unlike' : 'like';

        try {
            const response = await fetch(`api/like_poem.php?id=${poemId}&action=${action}`);
            const result = await response.json();
            
            if (result.success) {
                const count = (result.data && result.data.newLikes !== undefined) ? result.data.newLikes : (result.newLikes ?? 0);
                const countEl = document.getElementById('like-count');
                if (countEl) countEl.textContent = count;

                if (action === 'like') {
                    btn?.classList.add('liked');
                    localStorage.setItem('poem_liked_' + poemId, 'true');
                    hasLiked = true;
                    showToast('धन्यवाद! आपकी पसंद दर्ज हो गई है।');
                } else {
                    btn?.classList.remove('liked');
                    localStorage.removeItem('poem_liked_' + poemId);
                    hasLiked = false;
                    showToast('पसंद हटा दी गई।');
                }
            } else {
                showToast(result.message || 'त्रुटि उत्पन्न हुई।');
            }
        } catch (error) {
            console.error('Error toggling like for poem:', error);
            showToast('सर्वर से संपर्क नहीं हो सका।');
        }
    }

    // Copy Poem text to clipboard
    function copyPoemText() {
        const title = <?= json_encode($poem['title']) ?>;
        const author = <?= json_encode($poem['author_name']) ?>;
        const content = <?= json_encode($poem['content']) ?>;
        const url = window.location.href;

        const fullText = `${title}\n- ${author}\n\n${content}\n\nस्रोत: शब्द संचय (${url})`;

        navigator.clipboard.writeText(fullText).then(() => {
            showToast('कविता क्लिपबोर्ड पर कॉपी हो गई है!');
        }).catch(() => {
            showToast('कॉपी करने में त्रुटि हुई।');
        });
    }

    // Copy URL
    function copyPoemUrl() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('लिंक कॉपी हो गया!');
        }).catch(() => {
            showToast('लिंक कॉपी नहीं हो सका।');
        });
    }

    // Font size controls
    let currentFontSize = 1.35;
    function adjustPoemFontSize(delta) {
        currentFontSize += delta * 0.12;
        if (currentFontSize < 1.05) currentFontSize = 1.05;
        if (currentFontSize > 2.0) currentFontSize = 2.0;
        
        const versesContainer = document.getElementById('poem-verses-container');
        if (versesContainer) {
            versesContainer.style.fontSize = `${currentFontSize}rem`;
        }
    }

    function resetPoemFontSize() {
        currentFontSize = 1.35;
        const versesContainer = document.getElementById('poem-verses-container');
        if (versesContainer) {
            versesContainer.style.fontSize = `1.35rem`;
        }
    }

    // Share Handlers
    function shareOnWhatsApp() {
        const title = <?= json_encode($poem['title']) ?>;
        const author = <?= json_encode($poem['author_name']) ?>;
        const text = encodeURIComponent(`"${title}" - ${author}\nशब्द संचय पर यह सुंदर कविता पढ़ें:\n${window.location.href}`);
        window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
    }

    function shareOnTwitter() {
        const title = <?= json_encode($poem['title']) ?>;
        const author = <?= json_encode($poem['author_name']) ?>;
        const text = encodeURIComponent(`"${title}" - ${author} | शब्द संचय`);
        const url = encodeURIComponent(window.location.href);
        window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
    }

    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    }

    // Fetch and render comments
    async function fetchComments(poemId) {
        const countBadge = document.getElementById('comments-count-badge');
        const container = document.getElementById('comments-container');
        try {
            const response = await fetch(`api/get_comments.php?poem_id=${poemId}`);
            const comments = await response.json();
            
            if (countBadge) {
                countBadge.innerHTML = `<span>${comments.length} प्रतिक्रियाएँ</span>`;
            }

            if (!comments || comments.length === 0) {
                container.innerHTML = `
                    <div class="no-comments-box">
                        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.4; margin-bottom: 0.6rem;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <p class="no-comments-text">अभी तक कोई टिप्पणी नहीं आई है। आप पहले टिप्पणीकार बनें!</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = comments.map(comment => {
                const initialLetter = (comment.name && comment.name.trim().length > 0) ? comment.name.trim().charAt(0) : 'अ';
                return `
                    <div class="single-comment-card">
                        <div class="comment-author-row">
                            <div class="comment-initial-avatar">${escapeHtml(initialLetter)}</div>
                            <div class="comment-meta-info">
                                <h4 class="comment-user-name">${escapeHtml(comment.name)}</h4>
                                <span class="comment-timestamp">${comment.formatted_date || comment.created_at || ''}</span>
                            </div>
                        </div>
                        <div class="comment-body-content">
                            <p>${comment.comment}</p>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (error) {
            console.error('Error fetching comments:', error);
            if (container) {
                container.innerHTML = '<p class="text-error">टिप्पणियाँ लोड करने में समस्या आई।</p>';
            }
        }
    }

    // Submit Comment
    async function submitComment(event, poemId) {
        event.preventDefault();
        const submitBtn = document.getElementById('comment-submit-button');
        const nameInput = document.getElementById('comment-name');
        const emailInput = document.getElementById('comment-email');
        const textInput = document.getElementById('comment-text');

        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const comment = textInput.value.trim();

        if (!name || !email || !comment) {
            showToast('कृपया सभी आवश्यक फ़ील्ड भरें।');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span>सबमिट हो रहा है...</span>';

        try {
            const response = await fetch('api/add_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    poem_id: poemId,
                    content_type: 'poem',
                    name: name,
                    email: email,
                    comment: comment
                })
            });

            const result = await response.json();
            if (result.success) {
                document.getElementById('comment-form').reset();
                showToast('आपकी टिप्पणी सफलतापूर्वक प्रकाशित हो गई है!');
                fetchComments(poemId);
            } else {
                showToast(result.message || 'टिप्पणी सबमिट करने में समस्या आई।');
            }
        } catch (error) {
            console.error('Error submitting comment:', error);
            showToast('सर्वर से संपर्क करने में समस्या आई।');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <span>टिप्पणी सबमिट करें</span>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            `;
        }
    }

    // Fetch and render Related Poems (3-Column Grid matching poetry.php)
    async function fetchRelatedPoems(poemId, category) {
        const grid = document.getElementById('related-poems-grid');
        try {
            const response = await fetch(`api/get_related_poems.php?id=${poemId}&category=${encodeURIComponent(category)}`);
            const poems = await response.json();

            if (!poems || poems.length === 0) {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; opacity: 0.6; padding: 2rem 0;">कोई संबंधित कविता उपलब्ध नहीं है।</p>';
                return;
            }

            grid.innerHTML = poems.slice(0, 3).map(poem => {
                const displayDate = poem.formatted_date || (poem.created_at ? new Date(poem.created_at).toLocaleDateString('hi-IN') : '');
                const hasCustomPhoto = poem.image_url && 
                                       poem.image_url !== 'images/poetry-default.jpg' && 
                                       !poem.image_url.includes('default') && 
                                       poem.image_url.trim().length > 0;

                return `
                    <article class="poetry-grid-card">
                        <!-- Top Row: Category Tag & Live Metrics -->
                        <div class="card-top-row">
                            <span class="card-category-tag">${escapeHtml(poem.category || 'सामान्य')}</span>
                            <div class="card-metrics">
                                <span class="metric-item" title="देखा गया">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span>${poem.views || 0}</span>
                                </span>
                                <span class="metric-item" title="पसंद">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="#e11d48" stroke="#e11d48" stroke-width="1"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                    <span>${poem.likes || 0}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Middle Body: Poetry Details + Right Author Portrait (Square Frame) -->
                        <div class="card-body-layout">
                            <div class="card-main-content">
                                <h3 class="card-poem-title">
                                    <a href="poem.php?id=${poem.id}">${escapeHtml(poem.title)}</a>
                                </h3>
                                <p class="card-poem-excerpt">${escapeHtml(poem.content)}</p>
                            </div>

                            <div class="card-author-avatar-wrap">
                                ${hasCustomPhoto ? `
                                    <img src="${escapeHtml(poem.image_url)}" alt="${escapeHtml(poem.author_name)}" class="author-avatar-img" onerror="this.parentElement.innerHTML=getPoetDoodleSVG();">
                                ` : getPoetDoodleSVG()}
                            </div>
                        </div>

                        <!-- Bottom Footer: Author Info & Read Link -->
                        <div class="card-footer-row">
                            <div class="card-author-info">
                                <span class="card-author-name">${escapeHtml(poem.author_name)}</span>
                                <span class="card-date-stamp">${displayDate}</span>
                            </div>
                            <a href="poem.php?id=${poem.id}" class="card-read-action">
                                <span>पूरी कविता पढ़ें</span>
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </article>
                `;
            }).join('');
        } catch (error) {
            console.error('Error fetching related poems:', error);
            grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; opacity: 0.6;">संबंधित कविताएँ लोड करने में समस्या आई।</p>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const poemId = <?= $poem['id'] ?>;
        const category = <?= json_encode($poem['category']) ?>;
        restoreBookmarksState();
        fetchComments(poemId);
        fetchRelatedPoems(poemId, category);
    });
    </script>
</body>
</html>