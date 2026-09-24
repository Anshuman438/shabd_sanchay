<?php
require_once 'config.php';
require_once 'includes/helpers.php';

if (!isset($_GET['id'])) {
    header("Location: articles.php");
    exit();
}

$article_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();
$stmt->close();

if (!$article) {
    header("Location: articles.php");
    exit();
}

$page_title = htmlspecialchars($article['title']) . " - लेख | शब्द संचय";

// Increment views count safely
$conn->query("UPDATE articles SET views = views + 1 WHERE id = $article_id");
$article['views'] = ($article['views'] ?? 0) + 1;

// Read time calculation
$read_time = $article['read_time'] ?: max(3, ceil(mb_strlen(strip_tags($article['content'])) / 500));

// Formatted paragraphs
$paragraphs = array_filter(array_map('trim', explode("\n", $article['content'])));

$author_name = trim($article['author_name']);
$has_custom_photo = !empty($article['image_url']) && 
                     $article['image_url'] !== 'images/article-default.jpg' && 
                     !str_contains($article['image_url'], 'default');
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="<?= htmlspecialchars(mb_substr(str_replace(["\r", "\n"], ' ', $article['excerpt'] ?: $article['content']), 0, 160)) ?>...">
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="poem-single-page article-single-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="poem-detail-wrapper">
            <div class="container">
                
                <!-- Breadcrumbs -->
                <nav class="poem-breadcrumb" aria-label="ब्रेडक्रम्ब">
                    <a href="index.php">मुख्य पृष्ठ</a>
                    <span class="bc-sep">›</span>
                    <a href="articles.php">लेख</a>
                    <span class="bc-sep">›</span>
                    <a href="articles.php?category=<?= urlencode($article['category'] ?: 'सामान्य') ?>"><?= htmlspecialchars($article['category'] ?: 'सामान्य') ?></a>
                    <span class="bc-sep">›</span>
                    <span class="bc-current"><?= htmlspecialchars($article['title']) ?></span>
                </nav>

                <!-- Central Reading Parchment Card -->
                <article class="poem-reading-card article-reading-card">
                    <!-- Top Navigation & Category Pill -->
                    <div class="poem-reading-top-nav">
                        <a href="articles.php?category=<?= urlencode($article['category'] ?: 'सामान्य') ?>" class="poem-category-pill">
                            <span class="cat-dot"></span>
                            <span>लेख • <?= htmlspecialchars($article['category'] ?: 'सामान्य') ?></span>
                        </a>
                        <a href="articles.php" class="back-to-poems-link">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            <span>सभी लेख</span>
                        </a>
                    </div>

                    <!-- Article Header Area -->
                    <header class="poem-reading-header">
                        <h1 class="poem-reading-title"><?= htmlspecialchars($article['title']) ?></h1>
                        <div class="heading-artistic-underline"></div>
                        
                        <div class="poem-reading-meta">
                            <a href="articles.php?search=<?= urlencode($article['author_name']) ?>" class="meta-author-tag" title="लेखक के अन्य लेख देखें">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span><?= htmlspecialchars($article['author_name']) ?></span>
                            </a>
                            <span class="meta-separator">•</span>
                            <span class="meta-date-tag">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <span><?= format_hindi_date($article['created_at']) ?></span>
                            </span>
                            <span class="meta-separator">•</span>
                            <span class="meta-views-tag" title="देखा गया">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span><?= $article['views'] ?> बार पढ़ा गया</span>
                            </span>
                            <span class="meta-separator">•</span>
                            <span class="meta-stanza-tag">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span><?= $read_time ?> मिनट पठन</span>
                            </span>
                        </div>
                    </header>

                    <?php if (!empty($article['image_url']) && $article['image_url'] !== 'images/article-default.jpg'): ?>
                        <div class="article-featured-banner">
                            <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="article-banner-img">
                        </div>
                    <?php endif; ?>

                    <!-- Article Body Content -->
                    <div class="article-prose-wrapper" id="article-prose-container">
                        <?php if (!empty($article['excerpt'])): ?>
                            <div class="article-lead-excerpt">
                                <p><?= htmlspecialchars($article['excerpt']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="article-prose-paragraphs">
                            <?php foreach ($paragraphs as $p_idx => $para): 
                                $unique_para_key = "a{$article['id']}_p{$p_idx}";
                            ?>
                                <div class="article-para-row" id="<?= $unique_para_key ?>" data-para-key="<?= $unique_para_key ?>">
                                    <button type="button" class="btn-para-bookmark" onclick="toggleParaBookmark('<?= $unique_para_key ?>', <?= $article['id'] ?>, <?= htmlspecialchars(json_encode($article['title']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($article['author_name']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode(mb_substr($para, 0, 100)), ENT_QUOTES, 'UTF-8') ?>, this)" title="पैराग्राफ सहेजें / बुकमार्क करें">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                    </button>
                                    <p><?= nl2br(htmlspecialchars($para)) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Interactive Action Bar -->
                    <div class="poem-action-toolbar">
                        <div class="action-left-group">
                            <button type="button" class="poem-like-btn" id="article-like-button" onclick="likeCurrentArticle(<?= $article['id'] ?>)" title="लेख पसंद करें">
                                <svg class="like-heart-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                                <span class="like-label">पसंद</span>
                                <span class="like-counter" id="like-count"><?= $article['likes'] ?></span>
                            </button>

                            <!-- Bookmark Article Button -->
                            <button type="button" class="poem-bookmark-btn" id="article-bookmark-button" onclick="toggleWorkBookmark(<?= $article['id'] ?>, 'article', <?= htmlspecialchars(json_encode($article['title']), ENT_QUOTES, 'UTF-8') ?>)" title="लेख सहेजें / बुकमार्क करें">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <span id="article-bookmark-label">सहेजें</span>
                            </button>

                            <button type="button" class="poem-copy-btn" id="article-copy-button" onclick="copyArticleText()" title="लेख कॉपी करें">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                <span id="copy-btn-text">लेख कॉपी करें</span>
                            </button>
                        </div>

                        <div class="action-right-group">
                            <div class="font-size-adjuster" title="फॉन्ट आकार समायोजित करें">
                                <button type="button" class="font-btn" onclick="adjustArticleFontSize(-1)" title="छोटा फॉन्ट">A−</button>
                                <button type="button" class="font-btn" onclick="resetArticleFontSize()" title="सामान्य फॉन्ट">A</button>
                                <button type="button" class="font-btn" onclick="adjustArticleFontSize(1)" title="बड़ा फॉन्ट">A+</button>
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
                                <button type="button" class="share-icon-btn link-share" onclick="copyArticleUrl()" title="लिंक कॉपी करें" aria-label="Copy Link">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>

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
                        <div class="author-badge-eyebrow">लेखक परिचय • AUTHOR PROFILE</div>
                        <h2 class="author-showcase-name"><?= htmlspecialchars($article['author_name']) ?></h2>
                        <p class="author-showcase-bio">हिंदी साहित्य, संस्कृति और वैचारिक विषयों के चिंतनशील लेखक, जिनके विचार समाज और साहित्य को नई दृष्टि प्रदान करते हैं।</p>
                        <div class="author-showcase-footer">
                            <a href="articles.php?search=<?= urlencode($article['author_name']) ?>" class="author-explore-btn">
                                <span>इनके अन्य आलेख पढ़ें</span>
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
                                <h3 class="form-card-title">अपनी समीक्षा या विचार जोड़ें</h3>
                                <p class="form-card-subtitle">इस आलेख के विश्लेषण पर अपनी राय साझा करें।</p>
                            </div>
                            <form id="comment-form" onsubmit="submitComment(event, <?= $article['id'] ?>)">
                                <div class="form-row-dual">
                                    <div class="custom-form-group">
                                        <label for="comment-name">आपका नाम <span class="required-star">*</span></label>
                                        <input type="text" id="comment-name" class="custom-form-input" placeholder="उदा. अमित शर्मा" required>
                                    </div>
                                    <div class="custom-form-group">
                                        <label for="comment-email">ईमेल पता <span class="required-star">*</span></label>
                                        <input type="email" id="comment-email" class="custom-form-input" placeholder="उदा. amit@example.com" required>
                                    </div>
                                </div>
                                <div class="custom-form-group">
                                    <label for="comment-text">आपकी टिप्पणी <span class="required-star">*</span></label>
                                    <textarea id="comment-text" class="custom-form-textarea" rows="4" placeholder="इस लेख के संदर्भ में अपने विचार लिखें..." required></textarea>
                                </div>
                                <button type="submit" class="comment-submit-btn" id="comment-submit-button">
                                    <span>समीक्षा सबमिट करें</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                </button>
                            </form>
                        </div>

                        <div class="comments-list-wrap">
                            <h3 class="comments-list-title">हालिया टिप्पणियाँ</h3>
                            <div class="comments-list-container" id="comments-container">
                                <!-- Loaded via JS -->
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Related Articles Showcase -->
                <section class="related-poems-section">
                    <div class="related-section-header">
                        <span class="page-eyebrow">और भी पढ़ें • RELATED ARTICLES</span>
                        <h2 class="related-section-title">संबंधित विचारोत्तेजक आलेख</h2>
                        <div class="heading-artistic-underline"></div>
                    </div>

                    <div class="articles-grid" id="related-articles-grid">
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
        const btn = document.getElementById('article-bookmark-button');
        const label = document.getElementById('article-bookmark-label');
        let bookmarks = getWorkBookmarks();
        const key = `${type}_${id}`;
        const index = bookmarks.indexOf(key);

        if (index > -1) {
            bookmarks.splice(index, 1);
            if (btn) btn.classList.remove('bookmarked');
            if (label) label.textContent = 'सहेजें';
            showToast('लेख बुकमार्क से हटा दिया गया।');
        } else {
            bookmarks.push(key);
            if (btn) btn.classList.add('bookmarked');
            if (label) label.textContent = 'सहेजा गया';
            showToast('लेख सफलतापूर्वक सहेज लिया गया!');
        }
        localStorage.setItem('shabd_bookmarks', JSON.stringify(bookmarks));
    }

    function toggleParaBookmark(paraKey, articleId, articleTitle, authorName, paraSnippet, btnEl) {
        let lineBookmarks = getLineBookmarks();
        const row = document.getElementById(paraKey);

        if (lineBookmarks[paraKey]) {
            delete lineBookmarks[paraKey];
            if (row) row.classList.remove('para-bookmarked');
            showToast('पैराग्राफ बुकमार्क से हटा दिया गया।');
        } else {
            lineBookmarks[paraKey] = {
                articleId: articleId,
                articleTitle: articleTitle,
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
        const articleId = <?= $article['id'] ?>;
        // 1. Work bookmark
        const bookmarks = getWorkBookmarks();
        if (bookmarks.includes(`article_${articleId}`)) {
            const btn = document.getElementById('article-bookmark-button');
            const label = document.getElementById('article-bookmark-label');
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

    // Like / Unlike article functionality
    let hasLiked = localStorage.getItem('article_liked_' + <?= $article['id'] ?>) === 'true';
    if (hasLiked) {
        document.getElementById('article-like-button')?.classList.add('liked');
    }

    async function likeCurrentArticle(articleId) {
        const btn = document.getElementById('article-like-button');
        const action = hasLiked ? 'unlike' : 'like';

        try {
            const response = await fetch(`api/like_article.php?id=${articleId}&action=${action}`);
            const result = await response.json();
            
            if (result.success) {
                const count = (result.data && result.data.newLikes !== undefined) ? result.data.newLikes : (result.newLikes ?? 0);
                const countEl = document.getElementById('like-count');
                if (countEl) countEl.textContent = count;

                if (action === 'like') {
                    btn?.classList.add('liked');
                    localStorage.setItem('article_liked_' + articleId, 'true');
                    hasLiked = true;
                    showToast('धन्यवाद! आपकी पसंद दर्ज हो गई है।');
                } else {
                    btn?.classList.remove('liked');
                    localStorage.removeItem('article_liked_' + articleId);
                    hasLiked = false;
                    showToast('पसंद हटा दी गई।');
                }
            } else {
                showToast(result.message || 'त्रुटि उत्पन्न हुई।');
            }
        } catch (error) {
            console.error('Error toggling like for article:', error);
            showToast('सर्वर से संपर्क नहीं हो सका।');
        }
    }

    function copyArticleText() {
        const title = <?= json_encode($article['title']) ?>;
        const author = <?= json_encode($article['author_name']) ?>;
        const content = <?= json_encode($article['content']) ?>;
        const url = window.location.href;

        const fullText = `${title}\n- ${author}\n\n${content}\n\nस्रोत: शब्द संचय (${url})`;

        navigator.clipboard.writeText(fullText).then(() => {
            showToast('लेख क्लिपबोर्ड पर कॉपी हो गया!');
        }).catch(() => {
            showToast('कॉपी करने में समस्या आई।');
        });
    }

    function copyArticleUrl() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('लिंक कॉपी हो गया!');
        }).catch(() => {
            showToast('लिंक कॉपी नहीं हो सका।');
        });
    }

    // Font size adjustments
    let currentFontSize = 1.15;
    function adjustArticleFontSize(delta) {
        currentFontSize += delta * 0.08;
        if (currentFontSize < 0.95) currentFontSize = 0.95;
        if (currentFontSize > 1.7) currentFontSize = 1.7;
        
        const container = document.getElementById('article-prose-container');
        if (container) {
            container.style.fontSize = `${currentFontSize}rem`;
        }
    }

    function resetArticleFontSize() {
        currentFontSize = 1.15;
        const container = document.getElementById('article-prose-container');
        if (container) {
            container.style.fontSize = `1.15rem`;
        }
    }

    // Social Sharing
    function shareOnWhatsApp() {
        const title = <?= json_encode($article['title']) ?>;
        const author = <?= json_encode($article['author_name']) ?>;
        const text = encodeURIComponent(`"${title}" - ${author}\nशब्द संचय पर यह विचारोत्तेजक लेख पढ़ें:\n${window.location.href}`);
        window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank');
    }

    function shareOnTwitter() {
        const title = <?= json_encode($article['title']) ?>;
        const text = encodeURIComponent(`"${title}" | शब्द संचय`);
        const url = encodeURIComponent(window.location.href);
        window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
    }

    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    }

    // Comments
    async function fetchComments(articleId) {
        const countBadge = document.getElementById('comments-count-badge');
        const container = document.getElementById('comments-container');
        try {
            const response = await fetch(`api/get_comments.php?content_id=${articleId}&type=article`);
            const comments = await response.json();
            
            if (countBadge) {
                countBadge.innerHTML = `<span>${comments.length} प्रतिक्रियाएँ</span>`;
            }

            if (!comments || comments.length === 0) {
                container.innerHTML = `
                    <div class="no-comments-box">
                        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.4; margin-bottom: 0.6rem;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <p class="no-comments-text">अभी तक कोई टिप्पणी नहीं है। पहली समीक्षा साझा करें!</p>
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
        }
    }

    async function submitComment(event, articleId) {
        event.preventDefault();
        const submitBtn = document.getElementById('comment-submit-button');
        const name = document.getElementById('comment-name').value.trim();
        const email = document.getElementById('comment-email').value.trim();
        const comment = document.getElementById('comment-text').value.trim();

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
                    content_id: articleId,
                    content_type: 'article',
                    name: name,
                    email: email,
                    comment: comment
                })
            });

            const result = await response.json();
            if (result.success) {
                document.getElementById('comment-form').reset();
                showToast('आपकी समीक्षा सफलतापूर्वक प्रकाशित हो गई है!');
                fetchComments(articleId);
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

    // Related Articles
    async function fetchRelatedArticles(articleId, category) {
        const grid = document.getElementById('related-articles-grid');
        try {
            const response = await fetch('api/get_articles.php');
            const all = await response.json();
            const related = all.filter(a => a.id != articleId).slice(0, 3);

            if (!related || related.length === 0) {
                grid.innerHTML = '<p style="grid-column: 1/-1; text-align: center; opacity: 0.6;">कोई संबंधित आलेख उपलब्ध नहीं है।</p>';
                return;
            }

            grid.innerHTML = related.map(item => {
                const displayDate = item.formatted_date || (item.created_at ? new Date(item.created_at).toLocaleDateString('hi-IN') : '');
                const readTime = item.read_time || 5;
                const imageUrl = item.image_url && item.image_url.trim() !== '' ? item.image_url : 'images/article-default.jpg';

                return `
                    <article class="article-grid-card">
                        <div class="article-card-thumb-wrap">
                            <img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(item.title)}" class="article-card-thumb" loading="lazy" onerror="this.src='images/article-default.jpg';">
                            <span class="article-card-category-badge">${escapeHtml(item.category || 'सामान्य')}</span>
                            <span class="article-card-readtime-badge">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span>${readTime} मिनट</span>
                            </span>
                        </div>
                        <div class="article-card-body">
                            <h3 class="article-card-title">
                                <a href="article.php?id=${item.id}">${escapeHtml(item.title)}</a>
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
                            <a href="article.php?id=${item.id}" class="article-read-btn">
                                <span>पढ़ें</span>
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </article>
                `;
            }).join('');
        } catch (error) {
            console.error('Error fetching related articles:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const articleId = <?= $article['id'] ?>;
        const category = <?= json_encode($article['category']) ?>;
        restoreBookmarksState();
        fetchComments(articleId);
        fetchRelatedArticles(articleId, category);
    });
    </script>
</body>
</html>