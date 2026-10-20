<?php
require_once 'config.php';
require_once 'includes/helpers.php';
$page_title = "साहित्यिक आलेख एवं विचार - शब्द संचय";
$initial_category = isset($_GET['category']) ? htmlspecialchars(trim($_GET['category']), ENT_QUOTES, 'UTF-8') : 'all';

// Pre-fetch initial articles on server to eliminate initial client buffer spinner
$preloaded_articles = [];
try {
    if ($conn && !$conn->connect_error) {
        $res = $conn->query("SELECT id, title, excerpt, content, author_name, category, read_time, image_url, views, likes, created_at FROM articles ORDER BY created_at DESC LIMIT 60");
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $row['content'] = normalize_content_text($row['content']);
                $row['formatted_date'] = format_hindi_date($row['created_at']);
                if (empty($row['excerpt'])) {
                    $row['excerpt'] = make_excerpt($row['content'], 160);
                }
                $preloaded_articles[] = $row;
            }
        }
    }
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="preload" href="css/style.min.css?v=3.1" as="style">
    <link rel="stylesheet" href="css/style.min.css?v=3.1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Sans+Devanagari:wght@400;600;700&family=Noto+Serif+Devanagari:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="articles-index-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="articles-page-wrapper">
            <!-- Articles Hero Banner -->
            <section class="poetry-page-hero">
                <div class="container">
                    <div class="poetry-hero-content">
                        <span class="page-eyebrow">ज्ञान, विचार एवं संस्कृति • ESSAYS & ARTICLES</span>
                        <h1 class="page-main-title">हिंदी विचार एवं आलेख</h1>
                        <div class="heading-artistic-underline"></div>
                        <p class="page-lead-subtitle">साहित्य, इतिहास, दर्शन, कला और समाज के विविध आयामों पर गहन, प्रामाणिक और विचारोत्तेजक आलेखों का संकलन।</p>

                        <!-- Interactive Category Pills Filter Bar -->
                        <div class="category-pills-bar" id="category-pills-bar">
                            <button type="button" class="category-pill <?= ($initial_category === 'all' || empty($initial_category)) ? 'active' : '' ?>" data-category="all">
                                <span>सभी लेख</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'संस्कृति' ? 'active' : '' ?>" data-category="संस्कृति">
                                <span>संस्कृति</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'इतिहास' ? 'active' : '' ?>" data-category="इतिहास">
                                <span>इतिहास</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'दर्शन' ? 'active' : '' ?>" data-category="दर्शन">
                                <span>दर्शन</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'समाज' ? 'active' : '' ?>" data-category="समाज">
                                <span>समाज</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'साहित्य' ? 'active' : '' ?>" data-category="साहित्य">
                                <span>साहित्य</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'सामान्य' ? 'active' : '' ?>" data-category="सामान्य">
                                <span>सामान्य</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main Content Area with Toolbar & Grid -->
            <section class="poetry-content-section">
                <div class="container">
                    <!-- Glassmorphic Filter & Search Toolbar -->
                    <div class="poetry-toolbar-card">
                        <div class="toolbar-left-group">
                            <div class="custom-select-wrap">
                                <label for="sort-by" class="toolbar-label">क्रमबद्ध करें:</label>
                                <select id="sort-by" class="custom-select" aria-label="सॉर्ट विकल्प">
                                    <option value="newest">नवीनतम पहले</option>
                                    <option value="popular">सर्वाधिक लोकप्रिय</option>
                                    <option value="oldest">पुराने पहले</option>
                                </select>
                            </div>
                            <div class="results-count-badge" id="results-count">
                                <span>आलेख लोड हो रहे हैं...</span>
                            </div>
                        </div>

                        <div class="toolbar-search-box">
                            <svg class="search-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="article-search" class="search-input" placeholder="लेख, शीर्षक या लेखक का नाम खोजें..." autocomplete="off">
                            <button type="button" id="search-clear-btn" class="search-clear-btn" title="साफ़ करें" style="display:none;">✕</button>
                        </div>
                    </div>

                    <!-- Articles Cards Grid (3-Column Responsive) -->
                    <div class="articles-grid" id="articles-container">
                        <!-- Rendered via JS -->
                    </div>

                    <!-- Pagination Navigation -->
                    <div class="poetry-pagination-wrap" id="pagination">
                        <!-- Rendered via JS -->
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>

        <script>
    let currentPage = 1;
    const articlesPerPage = 6;
    let allArticles = <?= !empty($preloaded_articles) ? json_encode($preloaded_articles, JSON_UNESCAPED_UNICODE) : '[]' ?>;
    let currentCategory = '<?= $initial_category ?>';
    let searchDebounceTimer = null;
    let searchDebounceTimer = null;

    // Fetch all articles from API
    async function fetchArticles() {
        const container = document.getElementById('articles-container');
        const resultsCountEl = document.getElementById('results-count');

        try {
            const response = await fetch('api/get_articles.php');
            allArticles = await response.json();
            renderArticles(1);
        } catch (error) {
            console.error('Error fetching articles:', error);
            if (container) {
                container.innerHTML = `
                    <div class="poetry-empty-state">
                        <div class="empty-state-title">लेख लोड करने में समस्या आई</div>
                        <p class="empty-state-text">कृपया कुछ समय बाद पुनः प्रयास करें या पृष्ठ रीफ़्रेश करें।</p>
                    </div>
                `;
            }
            if (resultsCountEl) {
                resultsCountEl.innerHTML = '<span>0 लेख उपलब्ध</span>';
            }
        }
    }

    // Bookmarking helpers
    function getBookmarks() {
        try {
            return JSON.parse(localStorage.getItem('shabd_bookmarks') || '[]');
        } catch(e) {
            return [];
        }
    }

    function toggleBookmark(id, type, btnElement) {
        let bookmarks = getBookmarks();
        const key = `${type}_${id}`;
        const index = bookmarks.indexOf(key);
        
        if (index > -1) {
            bookmarks.splice(index, 1);
            btnElement.classList.remove('bookmarked');
            btnElement.setAttribute('title', 'सहेजें');
        } else {
            bookmarks.push(key);
            btnElement.classList.add('bookmarked');
            btnElement.setAttribute('title', 'सहेजा गया');
        }
        localStorage.setItem('shabd_bookmarks', JSON.stringify(bookmarks));
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

    function formatExcerpt(content) {
        if (!content) return '';
        const clean = content.replace(/<[^>]*>/g, '').trim();
        return clean.length > 140 ? clean.substring(0, 140) + '...' : clean;
    }

    function getArticleDoodleSVG() {
        return `
            <svg viewBox="0 0 80 80" width="46" height="46" fill="none" stroke="currentColor">
                <path d="M22 62 Q 35 48 55 22 Q 62 14 65 18 Q 63 26 50 42 Q 34 60 22 62 Z" stroke-width="2.2" stroke-linejoin="round" fill="rgba(200, 90, 23, 0.12)"/>
                <path d="M22 62 L 15 67 L 23 60" stroke-width="2.2" stroke-linecap="round"/>
                <path d="M38 38 L 48 48" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M57 18 Q62 12 66 14 Q65 19 60 22" stroke-width="1.6" fill="rgba(200, 90, 23, 0.25)"/>
            </svg>
        `;
    }

    // Filter and render articles with pagination
    function renderArticles(page = 1) {
        const container = document.getElementById('articles-container');
        const resultsCountEl = document.getElementById('results-count');
        const searchInput = document.getElementById('article-search');
        const clearBtn = document.getElementById('search-clear-btn');
        const sortBy = document.getElementById('sort-by').value;
        const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';

        // Show/hide search clear button
        if (clearBtn) {
            clearBtn.style.display = searchTerm.length > 0 ? 'inline-block' : 'none';
        }

        // Apply filters
        let filtered = allArticles.filter(article => {
            const matchesCategory = (currentCategory === 'all' || !currentCategory) || 
                                    (article.category && article.category.trim() === currentCategory.trim());
            
            if (!matchesCategory) return false;
            if (!searchTerm) return true;

            const title = (article.title || '').toLowerCase();
            const author = (article.author_name || '').toLowerCase();
            const content = (article.content || '').toLowerCase();
            const excerpt = (article.excerpt || '').toLowerCase();
            const category = (article.category || '').toLowerCase();

            return title.includes(searchTerm) || 
                   author.includes(searchTerm) || 
                   content.includes(searchTerm) || 
                   excerpt.includes(searchTerm) ||
                   category.includes(searchTerm);
        });

        // Apply Sorting
        filtered.sort((a, b) => {
            if (sortBy === 'newest') return new Date(b.created_at) - new Date(a.created_at);
            if (sortBy === 'oldest') return new Date(a.created_at) - new Date(b.created_at);
            if (sortBy === 'popular') return (Number(b.likes || 0) + Number(b.views || 0)) - (Number(a.likes || 0) + Number(a.views || 0));
            return 0;
        });

        // Update results counter
        if (resultsCountEl) {
            resultsCountEl.innerHTML = `<span>${filtered.length} लेख उपलब्ध</span>`;
        }

        // Pagination calculations
        const totalPages = Math.ceil(filtered.length / articlesPerPage);
        if (page > totalPages && totalPages > 0) page = 1;
        currentPage = page;
        const startIdx = (currentPage - 1) * articlesPerPage;
        const paginated = filtered.slice(startIdx, startIdx + articlesPerPage);

        // Render Empty State
        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="poetry-empty-state">
                    <div class="empty-state-title">कोई लेख नहीं मिला</div>
                    <p class="empty-state-text">आपके द्वारा चुने गए फ़िल्टर या खोज के अनुसार कोई रचना उपलब्ध नहीं है।</p>
                    <button type="button" class="btn-reset-filters" onclick="resetAllFilters()">सभी फ़िल्टर रीसेट करें</button>
                </div>
            `;
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        const bookmarks = getBookmarks();

                // Render Articles Cards Grid (Consistent Literary Poetry-Card Style - No Heavy Picture)
        container.innerHTML = paginated.map(article => {
            const displayDate = article.formatted_date || (article.created_at ? new Date(article.created_at).toLocaleDateString('hi-IN') : '');
            const readTime = article.read_time || 5;
            const hasCustomPhoto = article.image_url && 
                                   article.image_url !== 'images/article-default.jpg' && 
                                   !article.image_url.includes('default') && 
                                   !article.image_url.includes('picsum') &&
                                   article.image_url.trim().length > 0;
            const isBookmarked = bookmarks.includes(`article_${article.id}`);

            return `
                <article class="poetry-grid-card">
                    <!-- 1. Full-Width Top Row: Category Tag & Live Metrics -->
                    <div class="card-top-row">
                        <span class="card-category-tag">${escapeHtml(article.category || 'आलेख')}</span>
                        <div class="card-metrics">
                            <span class="metric-item" title="देखा गया">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span>${article.views || 0}</span>
                            </span>
                            <span class="metric-item" title="पसंद">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="#e11d48" stroke="#e11d48" stroke-width="1"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                <span>${article.likes || 0}</span>
                            </span>
                            <button type="button" class="btn-bookmark-action ${isBookmarked ? 'bookmarked' : ''}" onclick="toggleBookmark(${article.id}, 'article', this)" title="${isBookmarked ? 'सहेजा गया' : 'सहेजें'}">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Middle Body: Article Details + Right Author Portrait / Doodle -->
                    <div class="card-body-layout">
                        <div class="card-main-content">
                            <h2 class="card-poem-title">
                                <a href="article.php?id=${article.id}">${escapeHtml(article.title)}</a>
                            </h2>

                            <div class="card-poet-row">
                                <span class="poet-feather-icon">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                </span>
                                <span class="poet-name">${escapeHtml(article.author_name || 'अंशुमान सिंह')}</span>
                                <span class="card-readtime-pill">
                                    <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <span>${readTime} मिनट</span>
                                </span>
                            </div>

                            <div class="card-poem-excerpt">
                                ${formatExcerpt(article.excerpt || article.content)}
                            </div>
                        </div>

                        <!-- Right Side: Author Avatar / Doodle Area -->
                        <div class="card-author-avatar-wrap" title="${escapeHtml(article.author_name || 'लेखक')}">
                            ${hasCustomPhoto ? `
                                <img src="${escapeHtml(article.image_url)}" alt="${escapeHtml(article.author_name)}" class="author-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="author-doodle-fallback" style="display:none;">
                                    ${getArticleDoodleSVG()}
                                </div>
                            ` : `
                                <div class="author-doodle-fallback">
                                    ${getArticleDoodleSVG()}
                                </div>
                            `}
                        </div>
                    </div>

                    <!-- 3. Footer Row: Date & CTA -->
                    <div class="card-footer-row">
                        <span class="card-date">${displayDate}</span>
                        <a href="article.php?id=${article.id}" class="btn-card-read-more">
                            <span>पूरा लेख पढ़ें</span>
                            <span class="btn-arrow">→</span>
                        </a>
                    </div>
                </article>
            `;
        }).join('');

        renderPagination(totalPages);
    }

    // Render pagination controls
    function renderPagination(totalPages) {
        const paginationWrap = document.getElementById('pagination');
        if (!paginationWrap) return;

        if (totalPages <= 1) {
            paginationWrap.innerHTML = '';
            return;
        }

        let html = '';

        // Previous button
        if (currentPage > 1) {
            html += `
                <button type="button" class="page-nav-btn prev-btn" onclick="goToPage(${currentPage - 1})" aria-label="पिछला पृष्ठ">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>पिछला</span>
                </button>
            `;
        }

        // Page number buttons
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `
                    <button type="button" class="page-num-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})" aria-label="पृष्ठ ${i}">
                        ${i}
                    </button>
                `;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="page-ellipsis">…</span>`;
            }
        }

        // Next button
        if (currentPage < totalPages) {
            html += `
                <button type="button" class="page-nav-btn next-btn" onclick="goToPage(${currentPage + 1})" aria-label="अगला पृष्ठ">
                    <span>अगला</span>
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            `;
        }

        paginationWrap.innerHTML = html;
    }

    function goToPage(page) {
        renderArticles(page);
        const target = document.querySelector('.poetry-toolbar-card');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function resetAllFilters() {
        currentCategory = 'all';
        const searchInput = document.getElementById('article-search');
        if (searchInput) searchInput.value = '';
        const sortSelect = document.getElementById('sort-by');
        if (sortSelect) sortSelect.value = 'newest';

        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.toggle('active', pill.getAttribute('data-category') === 'all');
        });

        renderArticles(1);
    }

    // Initialize Event Listeners
    document.addEventListener('DOMContentLoaded', () => {
        fetchArticles();

        // Category Pills Click Handlers
        const pillsBar = document.getElementById('category-pills-bar');
        if (pillsBar) {
            pillsBar.addEventListener('click', (e) => {
                const button = e.target.closest('.category-pill');
                if (!button) return;

                document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
                button.classList.add('active');

                currentCategory = button.getAttribute('data-category');
                renderArticles(1);
            });
        }

        // Sort Dropdown Change
        const sortSelect = document.getElementById('sort-by');
        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                renderArticles(1);
            });
        }

        // Live Debounced Search Input
        const searchInput = document.getElementById('article-search');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    renderArticles(1);
                }, 250);
            });
        }

        // Clear Search Button
        const clearBtn = document.getElementById('search-clear-btn');
        if (clearBtn && searchInput) {
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchInput.focus();
                renderArticles(1);
            });
        }
    });
    </script>
</body>
</html>