<?php
require_once 'config.php';
require_once 'includes/helpers.php';

$page_title = "हिंदी कहानियाँ - शब्द संचय";
$initial_category = isset($_GET['category']) ? htmlspecialchars(trim($_GET['category']), ENT_QUOTES, 'UTF-8') : 'all';
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700;800&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="stories-index-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="stories-page-wrapper">
            <!-- Stories Hero Banner -->
            <section class="poetry-page-hero">
                <div class="container">
                    <div class="poetry-hero-content">
                        <span class="page-eyebrow">कथा साहित्य • SHORT STORIES & TALES</span>
                        <h1 class="page-main-title">हिंदी कहानियाँ</h1>
                        <div class="heading-artistic-underline"></div>
                        <p class="page-lead-subtitle">विभिन्न कालजयी कथाकारों और आधुनिक लेखकों की अमर, प्रेरक, मानवीय संवेदनाओं और जीवन के विविध रंगों से सराबोर कहानियों का संकलन।</p>

                        <!-- Interactive Category Pills Filter Bar -->
                        <div class="category-pills-bar" id="category-pills-bar">
                            <button type="button" class="category-pill <?= ($initial_category === 'all' || empty($initial_category)) ? 'active' : '' ?>" data-category="all">
                                <span>सभी कहानियाँ</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'सामाजिक' ? 'active' : '' ?>" data-category="सामाजिक">
                                <span>सामाजिक</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'संवेदना' ? 'active' : '' ?>" data-category="संवेदना">
                                <span>संवेदना</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'प्रेम एवं बलिदान' ? 'active' : '' ?>" data-category="प्रेम एवं बलिदान">
                                <span>प्रेम एवं बलिदान</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'मनोवैज्ञानिक' ? 'active' : '' ?>" data-category="मनोवैज्ञानिक">
                                <span>मनोवैज्ञानिक</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'ऐतिहासिक' ? 'active' : '' ?>" data-category="ऐतिहासिक">
                                <span>ऐतिहासिक</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'शिक्षाप्रद' ? 'active' : '' ?>" data-category="शिक्षाप्रद">
                                <span>शिक्षाप्रद</span>
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
                                <span>कहानियाँ लोड हो रही हैं...</span>
                            </div>
                        </div>

                        <div class="toolbar-search-box">
                            <svg class="search-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="story-search" class="search-input" placeholder="कहानी, शीर्षक या लेखक का नाम खोजें..." autocomplete="off">
                            <button type="button" id="search-clear-btn" class="search-clear-btn" title="साफ़ करें" style="display:none;">✕</button>
                        </div>
                    </div>

                    <!-- Stories Cards Grid (3-Column Responsive) -->
                    <div class="articles-grid" id="stories-container">
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
    const storiesPerPage = 6;
    let allStories = [];
    let currentCategory = '<?= $initial_category ?>';
    let searchDebounceTimer = null;

    // Fetch all stories from API
    async function fetchStories() {
        const container = document.getElementById('stories-container');
        const resultsCountEl = document.getElementById('results-count');

        try {
            const response = await fetch('api/get_stories.php');
            allStories = await response.json();
            renderStories(1);
        } catch (error) {
            console.error('Error fetching stories:', error);
            if (container) {
                container.innerHTML = `
                    <div class="poetry-empty-state">
                        <div class="empty-state-title">कहानियाँ लोड करने में समस्या आई</div>
                        <p class="empty-state-text">कृपया कुछ समय बाद पुनः प्रयास करें या पृष्ठ रीफ़्रेश करें।</p>
                    </div>
                `;
            }
            if (resultsCountEl) {
                resultsCountEl.innerHTML = '<span>0 कहानियाँ उपलब्ध</span>';
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

    // Story cover illustration fallback
    function getStoryFallbackImage(category) {
        const themes = {
            'सामाजिक': 'linear-gradient(135deg, #c85a17 0%, #7c2d12 100%)',
            'संवेदना': 'linear-gradient(135deg, #be123c 0%, #881337 100%)',
            'प्रेम एवं बलिदान': 'linear-gradient(135deg, #e11d48 0%, #9f1239 100%)',
            'मनोवैज्ञानिक': 'linear-gradient(135deg, #4338ca 0%, #312e81 100%)',
            'ऐतिहासिक': 'linear-gradient(135deg, #b45309 0%, #78350f 100%)',
            'शिक्षाप्रद': 'linear-gradient(135deg, #047857 0%, #064e3b 100%)'
        };
        return themes[category] || 'linear-gradient(135deg, #c85a17 0%, #9a3412 100%)';
    }

    // Filter and render stories with pagination
    function renderStories(page = 1) {
        const container = document.getElementById('stories-container');
        const resultsCountEl = document.getElementById('results-count');
        const searchInput = document.getElementById('story-search');
        const clearBtn = document.getElementById('search-clear-btn');
        const sortBy = document.getElementById('sort-by').value;
        const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';

        if (clearBtn) {
            clearBtn.style.display = searchTerm.length > 0 ? 'inline-block' : 'none';
        }

        // Apply filters
        let filtered = allStories.filter(story => {
            const matchesCategory = (currentCategory === 'all' || !currentCategory) || 
                                    (story.category && story.category.trim() === currentCategory.trim());
            
            if (!matchesCategory) return false;
            if (!searchTerm) return true;

            const title = (story.title || '').toLowerCase();
            const author = (story.author_name || '').toLowerCase();
            const content = (story.content || '').toLowerCase();
            const excerpt = (story.excerpt || '').toLowerCase();
            const category = (story.category || '').toLowerCase();

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
            resultsCountEl.innerHTML = `<span>${filtered.length} कहानियाँ उपलब्ध</span>`;
        }

        // Pagination calculations
        const totalPages = Math.ceil(filtered.length / storiesPerPage);
        if (page > totalPages && totalPages > 0) page = 1;
        currentPage = page;
        const startIdx = (currentPage - 1) * storiesPerPage;
        const paginated = filtered.slice(startIdx, startIdx + storiesPerPage);

        // Render Empty State
        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="poetry-empty-state">
                    <div class="empty-state-title">कोई कहानी नहीं मिली</div>
                    <p class="empty-state-text">आपके द्वारा चुने गए फ़िल्टर या खोज के अनुसार कोई रचना उपलब्ध नहीं है।</p>
                    <button type="button" class="btn-reset-filters" onclick="resetAllFilters()">सभी फ़िल्टर रीसेट करें</button>
                </div>
            `;
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        const bookmarks = getBookmarks();

        // Render Stories Cards Grid
        container.innerHTML = paginated.map(story => {
            const displayDate = story.formatted_date || (story.created_at ? new Date(story.created_at).toLocaleDateString('hi-IN') : '');
            const readTime = story.read_time || 8;
            const hasValidImage = story.image_url && 
                                  story.image_url.trim() !== '' && 
                                  !story.image_url.includes('picsum') && 
                                  story.image_url !== 'images/story-default.jpg';
            const isBookmarked = bookmarks.includes(`story_${story.id}`);

            return `
                <article class="article-grid-card story-grid-card">
                    <!-- Image Banner with Badges -->
                    <div class="article-card-thumb-wrap ${!hasValidImage ? 'story-gradient-thumb' : ''}" ${!hasValidImage ? `style="background: ${getStoryFallbackImage(story.category)};"` : ''}>
                        ${hasValidImage ? `
                            <img src="${escapeHtml(story.image_url)}" alt="${escapeHtml(story.title)}" class="article-card-thumb" loading="lazy" onerror="this.parentElement.classList.add('story-gradient-thumb'); this.style.display='none';">
                        ` : `
                            <div class="story-thumb-artistic">
                                <span class="story-thumb-icon">📖</span>
                                <span class="story-thumb-title">${escapeHtml(story.title)}</span>
                            </div>
                        `}
                        <span class="article-card-category-badge">${escapeHtml(story.category || 'कहानी')}</span>
                        <span class="article-card-readtime-badge">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>${readTime} मिनट पठन</span>
                        </span>
                    </div>

                    <!-- Story Body Content -->
                    <div class="article-card-body">
                        <h2 class="article-card-title">
                            <a href="story.php?id=${story.id}">${escapeHtml(story.title)}</a>
                        </h2>

                        <p class="article-card-excerpt">
                            ${escapeHtml(story.excerpt || story.content.substring(0, 160) + '...')}
                        </p>

                        <!-- Live Metrics -->
                        <div class="article-card-metrics">
                            <span class="metric-item" title="देखा गया">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span>${story.views || 0}</span>
                            </span>
                            <span class="metric-item" title="पसंद">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="#e11d48" stroke="#e11d48" stroke-width="1"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                <span>${story.likes || 0}</span>
                            </span>
                            <button type="button" class="btn-bookmark-action ${isBookmarked ? 'bookmarked' : ''}" onclick="toggleBookmark(${story.id}, 'story', this)" title="${isBookmarked ? 'सहेजा गया' : 'सहेजें'}">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Story Footer -->
                    <div class="article-card-footer">
                        <div class="article-author-meta">
                            <span class="article-author-name">${escapeHtml(story.author_name)}</span>
                            <span class="article-date-stamp">${displayDate}</span>
                        </div>
                        <a href="story.php?id=${story.id}" class="article-read-btn">
                            <span>पूरी कहानी पढ़ें</span>
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
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

        if (currentPage > 1) {
            html += `
                <button type="button" class="page-nav-btn prev-btn" onclick="goToPage(${currentPage - 1})" aria-label="पिछला पृष्ठ">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>पिछला</span>
                </button>
            `;
        }

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
        renderStories(page);
        const target = document.querySelector('.poetry-toolbar-card');
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function resetAllFilters() {
        currentCategory = 'all';
        const searchInput = document.getElementById('story-search');
        if (searchInput) searchInput.value = '';
        const sortSelect = document.getElementById('sort-by');
        if (sortSelect) sortSelect.value = 'newest';

        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.classList.toggle('active', pill.getAttribute('data-category') === 'all');
        });

        renderStories(1);
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchStories();

        const pillsBar = document.getElementById('category-pills-bar');
        if (pillsBar) {
            pillsBar.addEventListener('click', (e) => {
                const button = e.target.closest('.category-pill');
                if (!button) return;

                document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
                button.classList.add('active');

                currentCategory = button.getAttribute('data-category');
                renderStories(1);
            });
        }

        const sortSelect = document.getElementById('sort-by');
        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                renderStories(1);
            });
        }

        const searchInput = document.getElementById('story-search');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    renderStories(1);
                }, 250);
            });
        }

        const clearBtn = document.getElementById('search-clear-btn');
        if (clearBtn && searchInput) {
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchInput.focus();
                renderStories(1);
            });
        }
    });
    </script>
</body>
</html>
