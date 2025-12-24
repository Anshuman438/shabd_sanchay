<?php
require_once 'config.php';
$page_title = "हिंदी लेख";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - हिंदी साहित्य</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;700&display=swap" rel="stylesheet">

 
</head>
<body>
     <!-- Page Loader -->
     <div id="page-loader" aria-label="Loading" role="status" aria-live="polite">
        <div class="loader-spinner"></div>
    </div>
    <?php include 'header.php'; ?>

    <main class="container">
        <section class="page-header">
            <h1>हिंदी लेख</h1>
            <p>विचारोत्तेजक और ज्ञानवर्धक लेखों का संग्रह</p>
        </section>

        <section class="filter-section">
            <div class="filter-options">
                <select id="category-filter">
                    <option value="all">सभी श्रेणियाँ</option>
                    <option value="संस्कृति">संस्कृति</option>
                    <option value="इतिहास">इतिहास</option>
                    <option value="दर्शन">दर्शन</option>
                    <option value="समाज">समाज</option>
                </select>
                <select id="sort-by">
                    <option value="newest">नवीनतम पहले</option>
                    <option value="oldest">पुराने पहले</option>
                    <option value="popular">लोकप्रिय</option>
                </select>
            </div>
            <div class="search-box">
                <input type="text" id="article-search" placeholder="लेख खोजें...">
                <button id="search-button">खोजें</button>
            </div>
        </section>

        <section class="articles-list" id="articles-container">
            <!-- Articles loaded via JavaScript -->
        </section>

        <div class="pagination" id="pagination">
            <!-- Pagination loaded via JavaScript -->
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    let currentPage = 1;
const articlesPerPage = 5;
let allArticles = [];

// Combined loader and content loading
document.addEventListener('DOMContentLoaded', function() {
    // Show loader immediately
    document.body.classList.remove('loaded');
    
    // Set up fallback timeout
    const loadTimeout = setTimeout(() => {
        hideLoader();
    }, 3000); // 3 seconds maximum wait time

    // Function to hide loader
    function hideLoader() {
        document.body.classList.add('loaded');
        setTimeout(() => {
            document.getElementById('page-loader').style.display = 'none';
        }, 500);
        clearTimeout(loadTimeout);
    }

    // When window loads, hide loader
    window.addEventListener('load', hideLoader);
    
    // Fetch articles and handle loader
    fetchArticles().finally(hideLoader);
});

// Fetch all articles
async function fetchArticles() {
    const container = document.getElementById('articles-container');
    try {
        container.classList.add('loading');
        const response = await fetch('api/get_articles.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        allArticles = await response.json();
        renderArticles();
        setupPagination();
    } catch (error) {
        console.error('Error fetching articles:', error);
        document.getElementById('articles-container').innerHTML = 
            '<p class="error">लेख लोड करने में समस्या आई। कृपया बाद में पुनः प्रयास करें।</p>';
        // Re-throw error to ensure finally block still executes
        throw error;
    }
    finally {
        container.classList.remove('loading');
    }
}
    // Filter and render articles
    function renderArticles(page = 1) {
        const container = document.getElementById('articles-container');
        const categoryFilter = document.getElementById('category-filter').value;
        const searchTerm = document.getElementById('article-search').value.toLowerCase();
        const sortBy = document.getElementById('sort-by').value;
        
        // Filter articles
        let filteredArticles = allArticles.filter(article => {
            const matchesCategory = categoryFilter === 'all' || article.category === categoryFilter;
            const matchesSearch = article.title.toLowerCase().includes(searchTerm) || 
                                article.content.toLowerCase().includes(searchTerm) ||
                                article.excerpt.toLowerCase().includes(searchTerm) ||
                                article.author_name.toLowerCase().includes(searchTerm);
            return matchesCategory && matchesSearch;
        });

        // Sort articles
        filteredArticles.sort((a, b) => {
            if (sortBy === 'newest') return new Date(b.created_at) - new Date(a.created_at);
            if (sortBy === 'oldest') return new Date(a.created_at) - new Date(b.created_at);
            if (sortBy === 'popular') return b.likes - a.likes;
            return 0;
        });

        // Pagination
        const startIdx = (page - 1) * articlesPerPage;
        const paginatedArticles = filteredArticles.slice(startIdx, startIdx + articlesPerPage);
        currentPage = page;

        // Render articles
        if (paginatedArticles.length === 0) {
            container.innerHTML = '<p>कोई लेख उपलब्ध नहीं हैं।</p>';
        } else {
            container.innerHTML = paginatedArticles.map(article => `
    <article class="article-card">
        <div class="article-image">
            <img src="${article.image_url || 'images/article-default.jpg'}" alt="${article.title}" loading="lazy">
        </div>
        <div class="article-content">
            <h2><a href="article.php?id=${article.id}">${article.title}</a></h2>
            <p class="article-meta">लेखक: ${article.author_name} • ${new Date(article.created_at).toLocaleDateString('hi-IN')} • ${article.category}</p>
            <p>${article.content.split('\n').slice(0, 4).map(line => line.trim()).join('<br>')}</p>
            <div class="article-meta-bottom">
                <span class="read-time">⏱️ ${article.read_time || 5} मिनट पढ़ने</span>
                <span class="likes">❤️ ${article.likes}</span>
            </div>
            <a href="article.php?id=${article.id}" class="read-more">पूरा लेख पढ़ें</a>
        </div>
    </article>
`).join('');
        }
    }

    // Setup pagination
    function setupPagination() {
        const paginationDiv = document.getElementById('pagination');
        const totalPages = Math.ceil(allArticles.length / articlesPerPage);
        
        if (totalPages <= 1) {
            paginationDiv.innerHTML = '';
            return;
        }

        let paginationHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            paginationHTML += `
                <a href="#" class="page-numbers ${i === currentPage ? 'active' : ''}" 
                   onclick="changePage(${i}); return false;">${i}</a>
            `;
        }
        
        paginationDiv.innerHTML = paginationHTML;
    }

    // Change page
    function changePage(page) {
        renderArticles(page);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    // Event listeners for filters
    document.getElementById('category-filter').addEventListener('change', () => {
        renderArticles(1);
        setupPagination();
    });

    document.getElementById('sort-by').addEventListener('change', () => {
        renderArticles(1);
        setupPagination();
    });

    document.getElementById('search-button').addEventListener('click', () => {
        renderArticles(1);
        setupPagination();
    });

    document.getElementById('article-search').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            renderArticles(1);
            setupPagination();
        }
    });

    // Load articles when page loads
    document.addEventListener('DOMContentLoaded', fetchArticles);
    </script>
        <script src="js/theme.js"></script>

</body>
</html>