<?php
require_once 'config.php';
require_once 'includes/helpers.php';

if (!isset($_GET['id'])) {
    header("Location: stories.php");
    exit();
}

$story_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM stories WHERE id = ?");
$stmt->bind_param("i", $story_id);
$stmt->execute();
$result = $stmt->get_result();
$story = $result->fetch_assoc();
$stmt->close();

if (!$story) {
    header("Location: stories.php");
    exit();
}

$page_title = htmlspecialchars($story['title']) . " - कहानी (नोटबुक पठन) | शब्द संचय";

// Increment views count safely
$conn->query("UPDATE stories SET views = views + 1 WHERE id = $story_id");
$story['views'] = ($story['views'] ?? 0) + 1;

$read_time = $story['read_time'] ?: max(4, ceil(mb_strlen(strip_tags($story['content'])) / 450));

// Process story into Notebook pages (by delimiter ---PAGE--- or paragraphs)
$story_content = trim($story['content']);
if (strpos($story_content, '---PAGE---') !== false) {
    $raw_pages = explode('---PAGE---', $story_content);
    $pages = array_values(array_filter(array_map('trim', $raw_pages)));
} else {
    $raw_paragraphs = array_values(array_filter(array_map('trim', explode("\n", $story_content))));
    $chunk_size = max(2, ceil(count($raw_paragraphs) / 5));
    $chunks = array_chunk($raw_paragraphs, $chunk_size);
    $pages = [];
    foreach ($chunks as $chunk) {
        $pages[] = implode("\n\n", $chunk);
    }
}
$total_pages = max(1, count($pages));

$author_name = trim($story['author_name']);
$has_custom_photo = !empty($story['image_url']) && 
                     $story['image_url'] !== 'images/story-default.jpg' && 
                     !str_contains($story['image_url'], 'default') &&
                     !str_contains($story['image_url'], 'picsum');
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="<?= htmlspecialchars(mb_substr(str_replace(["\r", "\n"], ' ', $story['excerpt'] ?: $story['content']), 0, 160)) ?>...">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="poem-single-page story-single-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="poem-detail-wrapper">
            <div class="container">
                
                <!-- Breadcrumbs -->
                <nav class="poem-breadcrumb" aria-label="ब्रेडक्रम्ब">
                    <a href="index.php">मुख्य पृष्ठ</a>
                    <span class="bc-sep">›</span>
                    <a href="stories.php">कहानियाँ</a>
                    <span class="bc-sep">›</span>
                    <a href="stories.php?category=<?= urlencode($story['category'] ?: 'सामान्य') ?>"><?= htmlspecialchars($story['category'] ?: 'सामान्य') ?></a>
                    <span class="bc-sep">›</span>
                    <span class="bc-current"><?= htmlspecialchars($story['title']) ?></span>
                </nav>

                <!-- Top Story Header Banner -->
                <header class="poem-reading-header" style="text-align: center; margin-bottom: 2rem;">
                    <div class="poem-reading-top-nav" style="justify-content: center; margin-bottom: 1rem;">
                        <a href="stories.php?category=<?= urlencode($story['category'] ?: 'सामान्य') ?>" class="poem-category-pill">
                            <span class="cat-dot"></span>
                            <span>कथा साहित्य • <?= htmlspecialchars($story['category'] ?: 'सामान्य') ?></span>
                        </a>
                    </div>
                    <h1 class="poem-reading-title" style="font-size: 2.5rem; margin-bottom: 0.6rem;"><?= htmlspecialchars($story['title']) ?></h1>
                    <div class="heading-artistic-underline"></div>
                    
                    <div class="poem-reading-meta" style="justify-content: center; margin-top: 1rem;">
                        <a href="stories.php?search=<?= urlencode($story['author_name']) ?>" class="meta-author-tag" title="कथाकार की रचनाएँ देखें">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span><?= htmlspecialchars($story['author_name']) ?></span>
                        </a>
                        <span class="meta-separator">•</span>
                        <span class="meta-date-tag">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span><?= format_hindi_date($story['created_at']) ?></span>
                        </span>
                        <span class="meta-separator">•</span>
                        <span class="meta-views-tag" title="देखा गया">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <span><?= $story['views'] ?> बार पढ़ा गया</span>
                        </span>
                        <span class="meta-separator">•</span>
                        <span class="meta-stanza-tag">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span><?= $read_time ?> मिनट पठन (कुल <?= $total_pages ?> पृष्ठ)</span>
                        </span>
                    </div>
                </header>

                <?php if ($has_custom_photo): ?>
                    <div class="article-featured-banner" style="max-width: 960px; margin: 0 auto 2rem; border-radius: 16px; overflow: hidden; max-height: 380px;">
                        <img src="<?= htmlspecialchars($story['image_url']) ?>" alt="<?= htmlspecialchars($story['title']) ?>" class="article-banner-img" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                <?php endif; ?>

                <!-- =========================================================
                     NOTEBOOK FLIPBOOK CONTAINER
                ========================================================= -->
                <div class="story-notebook-container" id="story-notebook-wrapper">
                    
                    <!-- Top Control Bar (Reading Mode Toggle + Page Badge & Sound Toggle) -->
                    <div class="notebook-control-header">
                        <div class="notebook-mode-pills">
                            <button type="button" class="btn-mode-pill active" id="mode-notebook-btn" onclick="setReadingMode('notebook')">
                                <span>📖 नोटबुक फ्लिप मोड</span>
                            </button>
                            <button type="button" class="btn-mode-pill" id="mode-scroll-btn" onclick="setReadingMode('scroll')">
                                <span>📜 सतत पठन</span>
                            </button>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                            <button type="button" class="btn-sound-toggle" id="btn-sound-toggle" onclick="toggleSoundEffect()" title="पन्ना पलटने की ध्वनि टॉगल करें">
                                <span id="sound-icon">🔊</span>
                                <span id="sound-label">ध्वनि चालू</span>
                            </button>

                            <div class="notebook-page-badge" id="notebook-page-badge-wrap">
                                <span>पृष्ठ <strong id="current-page-num">1</strong> / <?= $total_pages ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="notebook-progress-track">
                        <div class="notebook-progress-fill" id="notebook-progress-bar" style="width: <?= round(100 / $total_pages) ?>%;"></div>
                    </div>

                    <!-- The 3D Vintage Parchment Notebook -->
                    <article class="story-notebook-book" id="notebook-book-element">
                        <div class="notebook-pages-viewport" id="notebook-pages-container">
                            
                            <?php foreach ($pages as $p_index => $page_text): 
                                $page_num = $p_index + 1;
                                $page_paras = array_filter(array_map('trim', explode("\n", $page_text)));
                            ?>
                                <section class="notebook-page-sheet <?= $page_num === 1 ? 'active' : '' ?>" id="story-page-<?= $page_num ?>" data-page-num="<?= $page_num ?>">
                                    <!-- Page Header in Notebook -->
                                    <div class="page-sheet-header">
                                        <span class="page-chapter-title"><?= htmlspecialchars($story['title']) ?> • भाग <?= $page_num ?></span>
                                        <span>शब्द संचय कथा-मंजूषा</span>
                                    </div>

                                    <!-- Page Body Content -->
                                    <div class="page-sheet-body <?= $page_num === 1 ? 'first-page' : '' ?>">
                                        <?php if ($page_num === 1 && !empty($story['excerpt'])): ?>
                                            <div class="article-lead-excerpt" style="margin-bottom: 1.8rem;">
                                                <p><?= htmlspecialchars($story['excerpt']) ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php foreach ($page_paras as $para_idx => $para): 
                                            $unique_para_key = "s{$story['id']}_p{$page_num}_{$para_idx}";
                                        ?>
                                            <div class="article-para-row" id="<?= $unique_para_key ?>" data-para-key="<?= $unique_para_key ?>">
                                                <button type="button" class="btn-para-bookmark" onclick="toggleParaBookmark('<?= $unique_para_key ?>', <?= $story['id'] ?>, <?= htmlspecialchars(json_encode($story['title']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($story['author_name']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode(mb_substr($para, 0, 100)), ENT_QUOTES, 'UTF-8') ?>, this)" title="पैराग्राफ सहेजें / बुकमार्क करें">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                                    </svg>
                                                </button>
                                                <p><?= nl2br(htmlspecialchars($para)) ?></p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Page Footer in Notebook -->
                                    <div class="page-sheet-footer">
                                        <span><?= htmlspecialchars($story['author_name']) ?></span>
                                        <span class="page-sheet-seal">~ ~ ❦ ~ ~</span>
                                        <span>पृष्ठ <?= $page_num ?> / <?= $total_pages ?></span>
                                    </div>
                                </section>
                            <?php endforeach; ?>

                        </div>
                    </article>

                    <!-- Flip Navigation & Quick Page Jump Row -->
                    <div class="notebook-flip-nav" id="notebook-flip-navigation">
                        <button type="button" class="btn-notebook-flip" id="btn-prev-page" onclick="goToPrevPage()" disabled>
                            <span>❮ पिछला पृष्ठ</span>
                        </button>

                        <div class="notebook-page-jump-row">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <button type="button" class="page-jump-pill <?= $i === 1 ? 'active' : '' ?>" id="jump-pill-<?= $i ?>" onclick="goToPage(<?= $i ?>)" title="पृष्ठ <?= $i ?> पर जाएँ">
                                    <?= $i ?>
                                </button>
                            <?php endfor; ?>
                        </div>

                        <button type="button" class="btn-notebook-flip" id="btn-next-page" onclick="goToNextPage()" <?= $total_pages <= 1 ? 'disabled' : '' ?>>
                            <span>अगला पृष्ठ ❯</span>
                        </button>
                    </div>

                    <!-- Interactive Action Bar (Like, Bookmark, Copy, Font Size, Share) -->
                    <div class="poem-action-toolbar" style="margin-top: 2rem;">
                        <div class="action-left-group">
                            <button type="button" class="poem-like-btn" id="story-like-button" onclick="likeCurrentStory(<?= $story['id'] ?>)" title="कहानी पसंद करें">
                                <svg class="like-heart-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                                <span class="like-label">पसंद</span>
                                <span class="like-counter" id="like-count"><?= $story['likes'] ?></span>
                            </button>

                            <!-- Bookmark Story Button -->
                            <button type="button" class="poem-bookmark-btn" id="story-bookmark-button" onclick="toggleWorkBookmark(<?= $story['id'] ?>, 'story', <?= htmlspecialchars(json_encode($story['title']), ENT_QUOTES, 'UTF-8') ?>)" title="कहानी सहेजें / बुकमार्क करें">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <span id="story-bookmark-label">सहेजें</span>
                            </button>

                            <button type="button" class="poem-copy-btn" id="story-copy-button" onclick="copyStoryText()" title="कहानी कॉपी करें">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                <span id="copy-btn-text">कहानी कॉपी करें</span>
                            </button>
                        </div>

                        <div class="action-right-group">
                            <div class="font-size-adjuster" title="फॉन्ट आकार समायोजित करें">
                                <button type="button" class="font-btn" onclick="adjustStoryFontSize(-1)" title="छोटा फॉन्ट">A−</button>
                                <button type="button" class="font-btn" onclick="resetStoryFontSize()" title="सामान्य फॉन्ट">A</button>
                                <button type="button" class="font-btn" onclick="adjustStoryFontSize(1)" title="बड़ा फॉन्ट">A+</button>
                            </div>

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
                                <button type="button" class="share-icon-btn link-share" onclick="copyStoryUrl()" title="लिंक कॉपी करें" aria-label="Copy Link">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Author Profile Showcase Card -->
                <section class="poem-author-showcase-card">
                    <div class="author-portrait-column">
                        <div class="card-author-avatar-wrap author-showcase-avatar">
                            <div class="author-doodle-fallback">
                                <svg class="poet-doodle-svg" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="32" cy="22" r="12"/>
                                    <path d="M16 52 C16 40 23 36 32 36 C41 36 48 40 48 52 Z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="author-details-column">
                        <div class="author-badge-eyebrow">कथाकार परिचय • STORY AUTHOR</div>
                        <h2 class="author-showcase-name"><?= htmlspecialchars($story['author_name']) ?></h2>
                        <p class="author-showcase-bio">हिंदी कथा-साहित्य के प्रख्यात लेखक, जिनकी कहानियाँ समाज, मानवीय संवेदनाओं और जीवन के अनछुए पहलुओं को सजीव रूप में प्रस्तुत करती हैं।</p>
                        <div class="author-showcase-footer">
                            <a href="stories.php?search=<?= urlencode($story['author_name']) ?>" class="author-explore-btn">
                                <span>इनकी अन्य कहानियाँ पढ़ें</span>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Comments & Discussions Section -->
                <section class="poem-discussion-section" id="comments">
                    <div class="discussion-header-row">
                        <div class="discussion-title-wrap">
                            <h2 class="discussion-title">पाठक प्रतिक्रियाएँ एवं समीक्षा</h2>
                            <div class="heading-artistic-underline" style="margin-left: 0;"></div>
                        </div>
                        <div class="comments-count-badge" id="comments-count-badge">
                            <span>प्रतिक्रियाएँ लोड हो रही हैं...</span>
                        </div>
                    </div>

                    <div class="discussion-grid">
                        <div class="comment-submission-card">
                            <div class="form-card-header">
                                <h3 class="form-card-title">अपनी समीक्षा साझा करें</h3>
                                <p class="form-card-subtitle">इस कहानी के कथानक और पात्रों पर अपने विचार रखें।</p>
                            </div>
                            <form id="comment-form" onsubmit="submitComment(event, <?= $story['id'] ?>)">
                                <div class="form-row-dual">
                                    <div class="custom-form-group">
                                        <label for="comment-name">आपका नाम <span class="required-star">*</span></label>
                                        <input type="text" id="comment-name" class="custom-form-input" placeholder="उदा. सुरेश वर्मा" required>
                                    </div>
                                    <div class="custom-form-group">
                                        <label for="comment-email">ईमेल पता <span class="required-star">*</span></label>
                                        <input type="email" id="comment-email" class="custom-form-input" placeholder="उदा. suresh@example.com" required>
                                    </div>
                                </div>
                                <div class="custom-form-group">
                                    <label for="comment-text">आपकी टिप्पणी <span class="required-star">*</span></label>
                                    <textarea id="comment-text" class="custom-form-textarea" rows="4" placeholder="कहानी के संदर्भ में अपने विचार यहाँ लिखें..." required></textarea>
                                </div>
                                <button type="submit" class="comment-submit-btn" id="comment-submit-button">
                                    <span>समीक्षा सबमिट करें</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                </button>
                            </form>
                        </div>

                        <div class="comments-list-wrap">
                            <h3 class="comments-list-title">हालिया समीक्षाएँ</h3>
                            <div class="comments-list-container" id="comments-container">
                                <!-- Loaded via JS -->
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Related Stories Section -->
                <section class="related-poems-section">
                    <div class="related-section-header">
                        <span class="page-eyebrow">कथा संकलन • RELATED STORIES</span>
                        <h2 class="related-section-title">अन्य प्रेरक कहानियाँ</h2>
                        <div class="heading-artistic-underline"></div>
                    </div>

                    <div class="articles-grid" id="related-stories-grid">
                        <!-- Loaded via JS -->
                    </div>
                </section>

            </div>
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast-notify" class="toast-notification" style="display:none;"></div>

    <?php include 'footer.php'; ?>

    <script>
    let currentPage = 1;
    const totalPages = <?= $total_pages ?>;
    let readingMode = 'notebook'; // 'notebook' or 'scroll'

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

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // =========================================================
    // REALISTIC PAGE FLIP SOUND SYNTHESIZER (Web Audio API)
    // =========================================================
    let audioCtx = null;
    let soundEnabled = true;

    function getAudioContext() {
        if (!audioCtx) {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                audioCtx = new AudioContextClass();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    function toggleSoundEffect() {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('sound-icon');
        const label = document.getElementById('sound-label');
        const btn = document.getElementById('btn-sound-toggle');
        
        if (soundEnabled) {
            if (icon) icon.textContent = '🔊';
            if (label) label.textContent = 'ध्वनि चालू';
            if (btn) btn.style.opacity = '1';
            showToast('पन्ना पलटने की ध्वनि सक्षम की गई।');
            playPageTurnSound();
        } else {
            if (icon) icon.textContent = '🔇';
            if (label) label.textContent = 'ध्वनि बंद';
            if (btn) btn.style.opacity = '0.65';
            showToast('ध्वनि म्यूट कर दी गई।');
        }
    }

    function playPageTurnSound() {
        if (!soundEnabled) return;
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            // Generate soft, tactile paper rustle noise
            const duration = 0.32;
            const bufferSize = Math.floor(ctx.sampleRate * duration);
            const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
            const data = buffer.getChannelData(0);

            let lastOut = 0.0;
            for (let i = 0; i < bufferSize; i++) {
                const white = (Math.random() * 2 - 1);
                // Pinkish noise filter approximation
                lastOut = (lastOut * 0.86) + (white * 0.14);
                data[i] = lastOut * (Math.random() > 0.35 ? 0.95 : 0.45);
            }

            const noiseSource = ctx.createBufferSource();
            noiseSource.buffer = buffer;

            // Bandpass filter to mimic authentic page friction frequencies
            const filter = ctx.createBiquadFilter();
            filter.type = 'bandpass';
            filter.frequency.setValueAtTime(1300, ctx.currentTime);
            filter.frequency.exponentialRampToValueAtTime(650, ctx.currentTime + duration);
            filter.Q.setValueAtTime(1.3, ctx.currentTime);

            // Lowpass filter to ensure soft natural texture without harsh hiss
            const lowpass = ctx.createBiquadFilter();
            lowpass.type = 'lowpass';
            lowpass.frequency.setValueAtTime(2800, ctx.currentTime);

            // Natural volume swell and fade envelope
            const gain = ctx.createGain();
            gain.gain.setValueAtTime(0.001, ctx.currentTime);
            gain.gain.linearRampToValueAtTime(0.24, ctx.currentTime + 0.05);
            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + duration);

            noiseSource.connect(filter);
            filter.connect(lowpass);
            lowpass.connect(gain);
            gain.connect(ctx.destination);

            noiseSource.start();
            noiseSource.stop(ctx.currentTime + duration);
        } catch(e) {
            console.warn('Audio page turn playback error:', e);
        }
    }

    // =========================================================
    // NOTEBOOK 3D PAGE-TURN LOGIC
    // =========================================================
    function goToPage(targetPage, direction = null) {
        if (targetPage < 1 || targetPage > totalPages) return;
        if (readingMode === 'scroll') return;

        const isChangingPage = (targetPage !== currentPage);
        const currentSheet = document.getElementById(`story-page-${currentPage}`);
        const nextSheet = document.getElementById(`story-page-${targetPage}`);
        if (!nextSheet) return;

        const isForward = direction ? direction === 'forward' : targetPage > currentPage;

        // Animate Page Turn
        if (currentSheet) {
            currentSheet.classList.remove('active', 'animate-turn-forward', 'animate-turn-backward');
        }

        nextSheet.classList.add('active');
        nextSheet.classList.remove('animate-turn-forward', 'animate-turn-backward');
        void nextSheet.offsetWidth; // trigger reflow
        nextSheet.classList.add(isForward ? 'animate-turn-forward' : 'animate-turn-backward');

        if (isChangingPage) {
            playPageTurnSound();
        }

        currentPage = targetPage;

        // Update Counter, Progress and Buttons
        const counterEl = document.getElementById('current-page-num');
        if (counterEl) counterEl.textContent = currentPage;

        const progressBar = document.getElementById('notebook-progress-bar');
        if (progressBar) {
            const pct = Math.round((currentPage / totalPages) * 100);
            progressBar.style.width = `${pct}%`;
        }

        const prevBtn = document.getElementById('btn-prev-page');
        const nextBtn = document.getElementById('btn-next-page');
        if (prevBtn) prevBtn.disabled = currentPage === 1;
        if (nextBtn) nextBtn.disabled = currentPage === totalPages;

        // Update Jump Pills
        document.querySelectorAll('.page-jump-pill').forEach(pill => pill.classList.remove('active'));
        const activePill = document.getElementById(`jump-pill-${currentPage}`);
        if (activePill) activePill.classList.add('active');

        // Smooth scroll to top of notebook
        const bookWrapper = document.getElementById('story-notebook-wrapper');
        if (bookWrapper) {
            const topOffset = bookWrapper.getBoundingClientRect().top + window.pageYOffset - 90;
            window.scrollTo({ top: topOffset, behavior: 'smooth' });
        }
    }

    function goToNextPage() {
        if (currentPage < totalPages) {
            goToPage(currentPage + 1, 'forward');
        }
    }

    function goToPrevPage() {
        if (currentPage > 1) {
            goToPage(currentPage - 1, 'backward');
        }
    }

    function setReadingMode(mode) {
        readingMode = mode;
        const wrapper = document.getElementById('story-notebook-wrapper');
        const notebookBtn = document.getElementById('mode-notebook-btn');
        const scrollBtn = document.getElementById('mode-scroll-btn');

        if (mode === 'scroll') {
            wrapper.classList.add('scroll-mode');
            notebookBtn.classList.remove('active');
            scrollBtn.classList.add('active');
            showToast('सतत स्क्रॉल मोड सक्रिय किया गया।');
        } else {
            wrapper.classList.remove('scroll-mode');
            scrollBtn.classList.remove('active');
            notebookBtn.classList.add('active');
            goToPage(currentPage);
            showToast('📖 नोटबुक फ्लिप मोड सक्रिय किया गया।');
        }
    }

    // Keyboard Arrow Keys Navigation
    document.addEventListener('keydown', (e) => {
        if (readingMode !== 'notebook') return;
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        if (e.key === 'ArrowRight' || e.key === 'PageDown') {
            goToNextPage();
        } else if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
            goToPrevPage();
        }
    });

    // Mobile Touch Swipe Navigation
    let touchStartX = 0;
    let touchEndX = 0;
    const bookEl = document.getElementById('notebook-book-element');
    if (bookEl) {
        bookEl.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        bookEl.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
    }

    function handleSwipe() {
        if (readingMode !== 'notebook') return;
        const swipeDistance = touchEndX - touchStartX;
        if (swipeDistance < -45) {
            goToNextPage();
        } else if (swipeDistance > 45) {
            goToPrevPage();
        }
    }

    // =========================================================
    // BOOKMARKING & LIKE FUNCTIONALITY
    // =========================================================
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
        const btn = document.getElementById('story-bookmark-button');
        const label = document.getElementById('story-bookmark-label');
        let bookmarks = getWorkBookmarks();
        const key = `${type}_${id}`;
        const index = bookmarks.indexOf(key);

        if (index > -1) {
            bookmarks.splice(index, 1);
            if (btn) btn.classList.remove('bookmarked');
            if (label) label.textContent = 'सहेजें';
            showToast('कहानी बुकमार्क से हटा दी गई।');
        } else {
            bookmarks.push(key);
            if (btn) btn.classList.add('bookmarked');
            if (label) label.textContent = 'सहेजा गया';
            showToast('कहानी सफलतापूर्वक सहेज ली गई!');
        }
        localStorage.setItem('shabd_bookmarks', JSON.stringify(bookmarks));
    }

    function toggleParaBookmark(paraKey, storyId, storyTitle, authorName, paraSnippet, btnEl) {
        let lineBookmarks = getLineBookmarks();
        const row = document.getElementById(paraKey);

        if (lineBookmarks[paraKey]) {
            delete lineBookmarks[paraKey];
            if (row) row.classList.remove('para-bookmarked');
            showToast('पैराग्राफ बुकमार्क से हटा दिया गया।');
        } else {
            lineBookmarks[paraKey] = {
                storyId: storyId,
                storyTitle: storyTitle,
                author: authorName,
                text: paraSnippet,
                savedAt: new Date().toISOString()
            };
            if (row) row.classList.add('para-bookmarked');
            showToast('पैराग्राफ सफलतापूर्वक सहेज लिया गया!');
        }
        localStorage.setItem('shabd_line_bookmarks', JSON.stringify(lineBookmarks));
    }

    function restoreBookmarksState() {
        const storyId = <?= $story['id'] ?>;
        // 1. Work bookmark
        const bookmarks = getWorkBookmarks();
        if (bookmarks.includes(`story_${storyId}`)) {
            const btn = document.getElementById('story-bookmark-button');
            const label = document.getElementById('story-bookmark-label');
            if (btn) btn.classList.add('bookmarked');
            if (label) label.textContent = 'सहेजा गया';
        }

        // 2. Paragraph bookmarks
        const lineBookmarks = getLineBookmarks();
        document.querySelectorAll('.article-para-row').forEach(row => {
            const key = row.dataset.paraKey;
            if (key && lineBookmarks[key]) {
                row.classList.add('para-bookmarked');
            }
        });
    }

    // Like / Unlike story
    let hasLiked = localStorage.getItem('story_liked_' + <?= $story['id'] ?>) === 'true';
    if (hasLiked) {
        document.getElementById('story-like-button')?.classList.add('liked');
    }

    async function likeCurrentStory(storyId) {
        const btn = document.getElementById('story-like-button');
        const action = hasLiked ? 'unlike' : 'like';

        try {
            const response = await fetch(`api/like_story.php?id=${storyId}&action=${action}`);
            const result = await response.json();
            
            if (result.success) {
                const count = (result.data && result.data.newLikes !== undefined) ? result.data.newLikes : (result.newLikes ?? 0);
                const countEl = document.getElementById('like-count');
                if (countEl) countEl.textContent = count;

                if (action === 'like') {
                    btn?.classList.add('liked');
                    localStorage.setItem('story_liked_' + storyId, 'true');
                    hasLiked = true;
                    showToast('धन्यवाद! आपकी पसंद दर्ज हो गई है।');
                } else {
                    btn?.classList.remove('liked');
                    localStorage.removeItem('story_liked_' + storyId);
                    hasLiked = false;
                    showToast('पसंद हटा दी गई।');
                }
            } else {
                showToast(result.message || 'त्रुटि उत्पन्न हुई।');
            }
        } catch (error) {
            console.error('Error toggling like:', error);
            showToast('सर्वर से संपर्क नहीं हो सका।');
        }
    }

    function copyStoryText() {
        const title = <?= json_encode($story['title']) ?>;
        const author = <?= json_encode($story['author_name']) ?>;
        const content = <?= json_encode($story['content']) ?>;
        const url = window.location.href;

        const fullText = `${title}\n- ${author}\n\n${content}\n\nस्रोत: शब्द संचय (${url})`;

        navigator.clipboard.writeText(fullText).then(() => {
            showToast('कहानी क्लिपबोर्ड पर कॉपी हो गई!');
        }).catch(() => {
            showToast('कॉपी करने में समस्या आई।');
        });
    }

    function copyStoryUrl() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('लिंक कॉपी हो गया!');
        }).catch(() => {
            showToast('लिंक कॉपी नहीं हो सका।');
        });
    }

    // Font size controls
    let currentFontSize = 1.14;
    function adjustStoryFontSize(delta) {
        currentFontSize += delta * 0.08;
        if (currentFontSize < 0.95) currentFontSize = 0.95;
        if (currentFontSize > 1.7) currentFontSize = 1.7;
        
        document.querySelectorAll('.page-sheet-body').forEach(body => {
            body.style.fontSize = `${currentFontSize}rem`;
        });
    }

    function resetStoryFontSize() {
        currentFontSize = 1.14;
        document.querySelectorAll('.page-sheet-body').forEach(body => {
            body.style.fontSize = `1.14rem`;
        });
    }

    // Share Handlers
    function shareOnWhatsApp() {
        const title = <?= json_encode($story['title']) ?>;
        const author = <?= json_encode($story['author_name']) ?>;
        const text = encodeURIComponent(`"${title}" - ${author}\nशब्द संचय पर यह मार्मिक कहानी पढ़ें:\n${window.location.href}`);
        window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
    }

    function shareOnTwitter() {
        const title = <?= json_encode($story['title']) ?>;
        const author = <?= json_encode($story['author_name']) ?>;
        const text = encodeURIComponent(`"${title}" - ${author} | शब्द संचय`);
        const url = encodeURIComponent(window.location.href);
        window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
    }

    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    }

    // Fetch and render comments
    async function fetchComments(storyId) {
        const countBadge = document.getElementById('comments-count-badge');
        const container = document.getElementById('comments-container');
        try {
            const response = await fetch(`api/get_comments.php?content_id=${storyId}&type=story`);
            const comments = await response.json();
            
            if (countBadge) {
                countBadge.innerHTML = `<span>${comments.length} समीक्षाएँ</span>`;
            }

            if (!comments || comments.length === 0) {
                container.innerHTML = `
                    <div class="no-comments-box">
                        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.4; margin-bottom: 0.6rem;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <p class="no-comments-text">अभी तक कोई समीक्षा नहीं आई है। अपनी समीक्षा साझा करने वाले पहले पाठक बनें!</p>
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
                container.innerHTML = '<p class="text-error">समीक्षाएँ लोड करने में समस्या आई।</p>';
            }
        }
    }

    // Submit Comment
    async function submitComment(event, storyId) {
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
                    content_id: storyId,
                    content_type: 'story',
                    name: name,
                    email: email,
                    comment: comment
                })
            });

            const result = await response.json();
            if (result.success) {
                document.getElementById('comment-form').reset();
                showToast('आपकी समीक्षा सफलतापूर्वक प्रकाशित हो गई है!');
                fetchComments(storyId);
            } else {
                showToast(result.message || 'समीक्षा सबमिट करने में समस्या आई।');
            }
        } catch (error) {
            console.error('Error submitting comment:', error);
            showToast('सर्वर से संपर्क करने में समस्या आई।');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <span>समीक्षा सबमिट करें</span>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            `;
        }
    }

    // Fetch and render Related Stories
    async function fetchRelatedStories(storyId, category) {
        const grid = document.getElementById('related-stories-grid');
        try {
            const response = await fetch(`api/get_stories.php`);
            const allStories = await response.json();

            const related = allStories.filter(s => s.id != storyId).slice(0, 3);

            if (!related || related.length === 0) {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; opacity: 0.6; padding: 2rem 0;">कोई अन्य कहानी उपलब्ध नहीं है।</p>';
                return;
            }

            grid.innerHTML = related.map(item => {
                const displayDate = item.formatted_date || (item.created_at ? new Date(item.created_at).toLocaleDateString('hi-IN') : '');
                const readTime = item.read_time || 8;
                const hasValidImage = item.image_url && 
                                      item.image_url.trim() !== '' && 
                                      !item.image_url.includes('picsum') && 
                                      item.image_url !== 'images/story-default.jpg';

                return `
                    <article class="article-grid-card story-grid-card">
                        <div class="article-card-thumb-wrap ${!hasValidImage ? 'story-gradient-thumb' : ''}">
                            ${hasValidImage ? `
                                <img src="${escapeHtml(item.image_url)}" alt="${escapeHtml(item.title)}" class="article-card-thumb" loading="lazy">
                            ` : `
                                <div class="story-thumb-artistic">
                                    <span class="story-thumb-icon">📖</span>
                                    <span class="story-thumb-title">${escapeHtml(item.title)}</span>
                                </div>
                            `}
                            <span class="article-card-category-badge">${escapeHtml(item.category || 'कहानी')}</span>
                            <span class="article-card-readtime-badge">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span>${readTime} मिनट पठन</span>
                            </span>
                        </div>
                        <div class="article-card-body">
                            <h3 class="article-card-title">
                                <a href="story.php?id=${item.id}">${escapeHtml(item.title)}</a>
                            </h3>
                            <p class="article-card-excerpt">
                                ${escapeHtml(item.excerpt || item.content.substring(0, 140) + '...')}
                            </p>
                        </div>
                        <div class="article-card-footer">
                            <div class="article-author-meta">
                                <span class="article-author-name">${escapeHtml(item.author_name)}</span>
                                <span class="article-date-stamp">${displayDate}</span>
                            </div>
                            <a href="story.php?id=${item.id}" class="article-read-btn">
                                <span>पढ़ें</span>
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </article>
                `;
            }).join('');
        } catch (error) {
            console.error('Error fetching related stories:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const storyId = <?= $story['id'] ?>;
        const category = <?= json_encode($story['category']) ?>;
        restoreBookmarksState();
        fetchComments(storyId);
        fetchRelatedStories(storyId, category);
    });
    </script>
</body>
</html>
