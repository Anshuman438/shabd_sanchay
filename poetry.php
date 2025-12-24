<?php
require_once 'config.php';
$page_title = "हिंदी कविताएँ";
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
            <h1>हिंदी कविताएँ</h1>
            <p>यहाँ आप विभिन्न विषयों पर लिखी गई सुंदर कविताएँ पढ़ सकते हैं</p>
        </section>

        <section class="filter-section">
            <div class="filter-options">
                <select id="category-filter">
                    <option value="all">सभी श्रेणियाँ</option>
                    <option value="प्रकृति">प्रकृति</option>
                    <option value="प्रेम">प्रेम</option>
                    <option value="इतिहास">इतिहास</option>
                    <option value="दार्शनिक">दार्शनिक</option>
                </select>
                <select id="sort-by">
                    <option value="newest">नवीनतम पहले</option>
                    <option value="oldest">पुराने पहले</option>
                    <option value="popular">लोकप्रिय</option>
                </select>
            </div>
            <div class="search-box">
                <input type="text" id="poetry-search" placeholder="कविता खोजें...">
                <button id="search-button">खोजें</button>
            </div>
        </section>

        <section class="poetry-list" id="poetry-container">
            <!-- Content loaded via JavaScript -->
        </section>

        <div class="pagination" id="pagination">
            <!-- Pagination loaded via JavaScript -->
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    let currentPage = 1;
    const poemsPerPage = 5;
    let allPoems = [];

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
        
        // Fetch poems and handle loader
        fetchPoems().finally(hideLoader);
    });

    // Fetch all poems
    async function fetchPoems() {
        try {
            const container = document.getElementById('poetry-container');
            container.classList.add('loading');
            
            const response = await fetch('api/get_poems.php');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            allPoems = await response.json();
            renderPoems();
            setupPagination();
        } catch (error) {
            console.error('Error fetching poems:', error);
            document.getElementById('poetry-container').innerHTML = 
                '<p class="error">कविताएँ लोड करने में समस्या आई। कृपया बाद में पुनः प्रयास करें।</p>';
            throw error;
        } finally {
            document.getElementById('poetry-container').classList.remove('loading');
        }
    }

    // Filter and render poems
    function renderPoems(page = 1) {
        const container = document.getElementById('poetry-container');
        const categoryFilter = document.getElementById('category-filter').value;
        const searchTerm = document.getElementById('poetry-search').value.toLowerCase();
        const sortBy = document.getElementById('sort-by').value;
        
        // Filter poems
        let filteredPoems = allPoems.filter(poem => {
            const matchesCategory = categoryFilter === 'all' || 
                                 (poem.category && poem.category.trim() === categoryFilter);
            const matchesSearch = searchTerm === '' || 
                                (poem.title && poem.title.toLowerCase().includes(searchTerm)) || 
                                (poem.content && poem.content.toLowerCase().includes(searchTerm)) ||
                                (poem.author_name && poem.author_name.toLowerCase().includes(searchTerm));
            return matchesCategory && matchesSearch;
        });

        // Sort poems
        filteredPoems.sort((a, b) => {
            if (sortBy === 'newest') return new Date(b.created_at) - new Date(a.created_at);
            if (sortBy === 'oldest') return new Date(a.created_at) - new Date(b.created_at);
            if (sortBy === 'popular') return (b.likes || 0) - (a.likes || 0);
            return 0;
        });

        // Pagination
        const startIdx = (page - 1) * poemsPerPage;
        const paginatedPoems = filteredPoems.slice(startIdx, startIdx + poemsPerPage);
        currentPage = page;

        // Render poems
        if (paginatedPoems.length === 0) {
            container.innerHTML = '<p>कोई कविताएँ उपलब्ध नहीं हैं।</p>';
        } else {
            container.innerHTML = paginatedPoems.map(poem => `
                <article class="poetry-card">
                    <div class="poetry-image">
                        <img src="${poem.image_url || 'images/article-default.jpg'}" alt="${poem.title}" loading="lazy">
                    </div>
                    <div class="poetry-content">
                        <h2><a href="poem.php?id=${poem.id}">${poem.title || 'अनाम कविता'}</a></h2>
                        <p class="poet-name">- ${poem.author_name || 'अज्ञात कवि'}</p>
                        <div class="poetry-text">
                            ${poem.content ? poem.content.split('\n').slice(0, 2).map(para => `<p>${para}</p>`).join('') : ''}
                        </div>
                        <div class="poetry-meta">
                            <span class="category">${poem.category || 'सामान्य'}</span>
                            <span class="date">${poem.created_at ? new Date(poem.created_at).toLocaleDateString('hi-IN') : 'तिथि अज्ञात'}</span>
                            <span class="likes">❤️ ${poem.likes || 0}</span>
                        </div>
                        <a href="poem.php?id=${poem.id}" class="read-more">पूरी कविता पढ़ें</a>
                    </div>
                </article>
            `).join('');
        }
    }

    // Setup pagination
    function setupPagination() {
        const paginationDiv = document.getElementById('pagination');
        const categoryFilter = document.getElementById('category-filter').value;
        const searchTerm = document.getElementById('poetry-search').value.toLowerCase();
        
        // Filter poems to calculate total pages correctly
        let filteredPoems = allPoems.filter(poem => {
            const matchesCategory = categoryFilter === 'all' || 
                                 (poem.category && poem.category.trim() === categoryFilter);
            const matchesSearch = searchTerm === '' || 
                                (poem.title && poem.title.toLowerCase().includes(searchTerm)) || 
                                (poem.content && poem.content.toLowerCase().includes(searchTerm)) ||
                                (poem.author_name && poem.author_name.toLowerCase().includes(searchTerm));
            return matchesCategory && matchesSearch;
        });

        const totalPages = Math.ceil(filteredPoems.length / poemsPerPage);
        
        if (totalPages <= 1) {
            paginationDiv.innerHTML = '';
            return;
        }

        let paginationHTML = '';
        
        // Previous button
        if (currentPage > 1) {
            paginationHTML += `<a href="#" class="page-numbers" onclick="changePage(${currentPage - 1}); return false;">&laquo;</a>`;
        }
        
        // Page numbers
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        if (startPage > 1) {
            paginationHTML += `<a href="#" class="page-numbers" onclick="changePage(1); return false;">1</a>`;
            if (startPage > 2) {
                paginationHTML += `<span class="page-dots">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <a href="#" class="page-numbers ${i === currentPage ? 'active' : ''}" 
                   onclick="changePage(${i}); return false;">${i}</a>
            `;
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<span class="page-dots">...</span>`;
            }
            paginationHTML += `<a href="#" class="page-numbers" onclick="changePage(${totalPages}); return false;">${totalPages}</a>`;
        }
        
        // Next button
        if (currentPage < totalPages) {
            paginationHTML += `<a href="#" class="page-numbers" onclick="changePage(${currentPage + 1}); return false;">&raquo;</a>`;
        }
        
        paginationDiv.innerHTML = paginationHTML;
    }

    // Change page
    function changePage(page) {
        renderPoems(page);
        window.scrollTo({top: 0, behavior: 'smooth'});
    }

    // Event listeners for filters
    document.getElementById('category-filter').addEventListener('change', () => {
        currentPage = 1;
        renderPoems(currentPage);
        setupPagination();
    });

    document.getElementById('sort-by').addEventListener('change', () => {
        currentPage = 1;
        renderPoems(currentPage);
        setupPagination();
    });

    document.getElementById('search-button').addEventListener('click', () => {
        currentPage = 1;
        renderPoems(currentPage);
        setupPagination();
    });

    document.getElementById('poetry-search').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            currentPage = 1;
            renderPoems(currentPage);
            setupPagination();
        }
    });
    </script>
    <script src="js/theme.js"></script>
</body>
</html>