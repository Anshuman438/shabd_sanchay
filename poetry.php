<?php
require_once 'config.php';
$page_title = "हिंदी कविताएँ - शब्द संचय";
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
<body class="poetry-index-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="poetry-page-wrapper">
            <!-- Poetry Hero Banner -->
            <section class="poetry-page-hero">
                <div class="container">
                    <div class="poetry-hero-content">
                        <span class="page-eyebrow">साहित्यिक संकलन • POETRY COLLECTION</span>
                        <h1 class="page-main-title">हिंदी कविताएँ</h1>
                        <div class="heading-artistic-underline"></div>
                        <p class="page-lead-subtitle">अनुभूतियों, छंदों और भावों का सुरम्य संसार - जहाँ हर पंक्ति एक नई संवेदना और विचारों का विस्तार है।</p>

                        <!-- Interactive Category Pills Filter Bar -->
                        <div class="category-pills-bar" id="category-pills-bar">
                            <button type="button" class="category-pill <?= ($initial_category === 'all' || empty($initial_category)) ? 'active' : '' ?>" data-category="all">
                                <span>सभी कविताएँ</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'प्रकृति' ? 'active' : '' ?>" data-category="प्रकृति">
                                <span>प्रकृति</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'प्रेम' ? 'active' : '' ?>" data-category="प्रेम">
                                <span>प्रेम</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'प्रेरणादायक' ? 'active' : '' ?>" data-category="प्रेरणादायक">
                                <span>प्रेरणादायक</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'देशभक्ति' ? 'active' : '' ?>" data-category="देशभक्ति">
                                <span>देशभक्ति</span>
                            </button>
                            <button type="button" class="category-pill <?= $initial_category === 'दार्शनिक' ? 'active' : '' ?>" data-category="दार्शनिक">
                                <span>दार्शनिक</span>
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
                                <span>रचनाएँ लोड हो रही हैं...</span>
                            </div>
                        </div>

                        <div class="toolbar-search-box">
                            <svg class="search-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="poetry-search" class="search-input" placeholder="कविता, शीर्षक या कवि का नाम खोजें..." autocomplete="off">
                            <button type="button" id="search-clear-btn" class="search-clear-btn" title="साफ़ करें" style="display:none;">✕</button>
                        </div>
                    </div>

                    <!-- Poetry Cards Grid (3-Column Responsive) -->
                    <div class="poetry-grid" id="poetry-container">
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
    const poemsPerPage = 6;
    let allPoems = [];
    let currentCategory = '<?= $initial_category ?>';
    let searchDebounceTimer = null;

    // Fetch all poems from API
    async function fetchPoems() {
        const container = document.getElementById('poetry-container');
        container.innerHTML = `
            <div class="poetry-empty-state">
                <div class="empty-state-title">कविताएँ लोड की जा रही हैं...</div>
                <p class="empty-state-text">साहित्य संचय से चयनित रचनाएँ लाई जा रही हैं</p>
            </div>
        `;
        try {
            const response = await fetch('api/get_poems.php');
            allPoems = await response.json();
            renderPoems(1);
        } catch (error) {
            console.error('Error fetching poems:', error);
            container.innerHTML = `
                <div class="poetry-empty-state">
                    <div class="empty-state-title">रचनाएँ लोड करने में समस्या आई</div>
                    <p class="empty-state-text">कृपया कुछ समय बाद पुनः प्रयास करें या पृष्ठ को रीफ़्रेश करें।</p>
                    <button type="button" class="btn-reset-filters" onclick="fetchPoems()">पुनः प्रयास करें</button>
                </div>
            `;
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

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Format Excerpt Lines cleanly (2-3 stanzas/lines)
    function formatExcerpt(content) {
        if (!content) return '';
        const lines = content.split('\n').map(l => l.trim()).filter(l => l.length > 0);
        const excerptLines = lines.slice(0, 2);
        return excerptLines.map(line => `<p>${escapeHtml(line)}</p>`).join('');
    }

    // Return Hand-Drawn Poet Doodle SVG (Fallback when no author photo exists)
    function getPoetDoodleSVG() {
        return `
            <svg viewBox="0 0 80 80" class="poet-doodle-svg" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <!-- Head / Face Outline with Warm Tint -->
                <path d="M26 34 C24 22, 34 16, 40 16 C46 16, 56 22, 54 34 C54 44, 48 50, 40 50 C32 50, 26 44, 26 34 Z" fill="rgba(200, 90, 23, 0.08)"/>
                <!-- Classic Poet Pagdi / Hair Flow -->
                <path d="M24 28 C25 18, 32 12, 40 12 C48 12, 55 18, 56 28" stroke-width="2.6"/>
                <path d="M28 20 Q40 14 52 20" stroke-width="1.8"/>
                <!-- Eyeglasses / Reading Spectacles -->
                <circle cx="34" cy="33" r="4.2" stroke-width="1.8"/>
                <circle cx="46" cy="33" r="4.2" stroke-width="1.8"/>
                <line x1="38.2" y1="33" x2="41.8" y2="33" stroke-width="1.8"/>
                <!-- Eyes & Gentle Literary Smile -->
                <circle cx="34" cy="33" r="1.1" fill="currentColor"/>
                <circle cx="46" cy="33" r="1.1" fill="currentColor"/>
                <path d="M37 42 Q40 45 43 42" stroke-width="2"/>
                <!-- Collar & Kurta / Shawl -->
                <path d="M18 68 C22 56, 30 52, 40 52 C50 52, 58 56, 62 68" stroke-width="2.4"/>
                <path d="M36 52 L40 60 L44 52" stroke-width="1.8"/>
                <!-- Tiny Quill / Feather Accent -->
                <path d="M57 18 Q62 12 66 14 Q65 19 60 22" stroke-width="1.6" fill="rgba(200, 90, 23, 0.25)"/>
            </svg>
        `;
    }

    // Filter and render poems
    function renderPoems(page = 1) {
        const container = document.getElementById('poetry-container');
        const searchTerm = document.getElementById('poetry-search').value.trim().toLowerCase();
        const sortBy = document.getElementById('sort-by').value;
        const resultsCountEl = document.getElementById('results-count');
        const clearBtn = document.getElementById('search-clear-btn');

        // Toggle clear search button
        if (clearBtn) {
            clearBtn.style.display = searchTerm.length > 0 ? 'inline-block' : 'none';
        }

        // Filter
        let filtered = allPoems.filter(poem => {
            const matchesCategory = currentCategory === 'all' || poem.category === currentCategory;
            const title = (poem.title || '').toLowerCase();
            const content = (poem.content || '').toLowerCase();
            const author = (poem.author_name || '').toLowerCase();
            const matchesSearch = !searchTerm || title.includes(searchTerm) || content.includes(searchTerm) || author.includes(searchTerm);
            return matchesCategory && matchesSearch;
        });

        // Sort
        filtered.sort((a, b) => {
            if (sortBy === 'newest') return new Date(b.created_at) - new Date(a.created_at);
            if (sortBy === 'oldest') return new Date(a.created_at) - new Date(b.created_at);
            if (sortBy === 'popular') return (Number(b.likes) + Number(b.views)) - (Number(a.likes) + Number(a.views));
            return 0;
        });

        // Update results counter
        if (resultsCountEl) {
            resultsCountEl.innerHTML = `<span>${filtered.length} रचनाएँ उपलब्ध</span>`;
        }

        // Pagination calculations
        const totalPages = Math.ceil(filtered.length / poemsPerPage);
        if (page > totalPages && totalPages > 0) page = 1;
        currentPage = page;
        const startIdx = (currentPage - 1) * poemsPerPage;
        const paginated = filtered.slice(startIdx, startIdx + poemsPerPage);

        // Render Empty State
        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="poetry-empty-state">
                    <div class="empty-state-title">कोई कविता नहीं मिली</div>
                    <p class="empty-state-text">आपके द्वारा चुने गए फ़िल्टर या खोज के अनुसार कोई रचना उपलब्ध नहीं है।</p>
                    <button type="button" class="btn-reset-filters" onclick="resetAllFilters()">सभी फ़िल्टर रीसेट करें</button>
                </div>
            `;
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        const bookmarks = getBookmarks();

        // Render Cards Grid
        container.innerHTML = paginated.map(poem => {
            const displayDate = poem.formatted_date || (poem.created_at ? new Date(poem.created_at).toLocaleDateString('hi-IN') : '');
            const hasCustomPhoto = poem.image_url && 
                                   poem.image_url !== 'images/poetry-default.jpg' && 
                                   !poem.image_url.includes('default') && 
                                   poem.image_url.trim().length > 0;
            const isBookmarked = bookmarks.includes(`poem_${poem.id}`);

            return `
                <article class="poetry-grid-card">
                    <!-- 1. Full-Width Top Row: Category Tag & Live Metrics -->
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
                            <button type="button" class="btn-bookmark-action ${isBookmarked ? 'bookmarked' : ''}" onclick="toggleBookmark(${poem.id}, 'poem', this)" title="${isBookmarked ? 'सहेजा गया' : 'सहेजें'}">
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- 2. Middle Body: Poetry Details + Right Author Portrait -->
                    <div class="card-body-layout">
                        <div class="card-main-content">
                            <h2 class="card-poem-title">
                                <a href="poem.php?id=${poem.id}">${escapeHtml(poem.title)}</a>
                            </h2>

                            <div class="card-poet-row">
                                <span class="poet-feather-icon">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                </span>
                                <span class="poet-name">${escapeHtml(poem.author_name || 'अज्ञात')}</span>
                            </div>

                            <div class="card-poem-excerpt">
                                ${formatExcerpt(poem.content)}
                            </div>
                        </div>

                        <!-- Right Side: Author Avatar / Doodle Area -->
                        <div class="card-author-avatar-wrap" title="${escapeHtml(poem.author_name || 'रचनाकार')}">
                            ${hasCustomPhoto ? `
                                <img src="${escapeHtml(poem.image_url)}" alt="${escapeHtml(poem.author_name)}" class="author-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="author-doodle-fallback" style="display:none;">
                                    ${getPoetDoodleSVG()}
                                </div>
                            ` : `
                                <div class="author-doodle-fallback">
                                    ${getPoetDoodleSVG()}
                                </div>
                            `}
                        </div>
                    </div>

                    <!-- 3. Footer Row: Date & CTA -->
                    <div class="card-footer-row">
                        <span class="card-date">${displayDate}</span>
                        <a href="poem.php?id=${poem.id}" class="btn-card-read-more">
                            <span>पूरी कविता पढ़ें</span>
                            <span class="btn-arrow">→</span>
                        </a>
                    </div>
                </article>
            `;
        }).join('');

        renderPagination(filtered.length);
    }

    // Render numbered pagination
    function renderPagination(totalItems) {
        const paginationDiv = document.getElementById('pagination');
        const totalPages = Math.ceil(totalItems / poemsPerPage);

        if (totalPages <= 1) {
            paginationDiv.innerHTML = '';
            return;
        }

        let html = '';
        if (currentPage > 1) {
            html += `<button type="button" class="page-pill" onclick="goToPage(${currentPage - 1})" aria-label="पिछला पृष्ठ">←</button>`;
        }

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `<button type="button" class="page-pill ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="page-pill" style="border:none; background:transparent; cursor:default;">…</span>`;
            }
        }

        if (currentPage < totalPages) {
            html += `<button type="button" class="page-pill" onclick="goToPage(${currentPage + 1})" aria-label="अगला पृष्ठ">→</button>`;
        }

        paginationDiv.innerHTML = html;
    }

    function goToPage(page) {
        renderPoems(page);
        const toolbar = document.querySelector('.poetry-toolbar-card');
        if (toolbar) {
            toolbar.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Reset filters
    function resetAllFilters() {
        currentCategory = 'all';
        document.querySelectorAll('.category-pill').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.category === 'all');
        });
        document.getElementById('poetry-search').value = '';
        document.getElementById('sort-by').value = 'newest';
        renderPoems(1);
    }

    // Setup Category Pill Clicks
    document.querySelectorAll('.category-pill').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.dataset.category;
            renderPoems(1);
        });
    });

    // Real-time debounced search
    document.getElementById('poetry-search').addEventListener('input', () => {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
            renderPoems(1);
        }, 220);
    });

    // Clear search button
    document.getElementById('search-clear-btn').addEventListener('click', () => {
        document.getElementById('poetry-search').value = '';
        renderPoems(1);
    });

    // Sort change
    document.getElementById('sort-by').addEventListener('change', () => {
        renderPoems(1);
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', fetchPoems);
    </script>
</body>
</html>