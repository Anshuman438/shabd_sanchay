<?php
// play.php
require_once 'config.php';
require_once 'includes/helpers.php';
$page_title = "हिंदी नाटक व एकांकी";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - शब्द संचय</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;700&family=Noto+Sans+Devanagari:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <section class="page-header">
            <h1>हिंदी नाटक व एकांकी</h1>
            <p>हिंदी नाटक, एकांकी और संवाद रचनाओं का संग्रह</p>
        </section>

        <!-- Search and Filter Bar -->
        <section class="filter-section">
            <div class="filter-options">
                <select id="category-filter">
                    <option value="all">सभी शैलियाँ</option>
                    <option value="ऐतिहासिक नाटक">ऐतिहासिक नाटक</option>
                    <option value="गीतिनाट्य">गीतिनाट्य</option>
                    <option value="एकांकी">एकांकी</option>
                    <option value="सामाजिक नाटक">सामाजिक नाटक</option>
                    <option value="व्यंग्य">व्यंग्य</option>
                </select>
                <select id="sort-by">
                    <option value="newest">नवीनतम पहले</option>
                    <option value="popular">लोकप्रिय</option>
                    <option value="oldest">पुराने पहले</option>
                </select>
            </div>
            <div class="search-box">
                <input type="text" id="plays-search" placeholder="नाटक खोजें...">
                <button id="search-button">खोजें</button>
            </div>
        </section>

        <!-- Plays List Container -->
        <section class="posts-grid" id="plays-container">
            <!-- Rendered via JS -->
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    async function loadPlays() {
        const cat = document.getElementById('category-filter').value;
        const sort = document.getElementById('sort-by').value;
        const search = document.getElementById('plays-search').value.trim();
        const container = document.getElementById('plays-container');

        try {
            const url = `api/get_plays.php?category=${encodeURIComponent(cat)}&sort=${encodeURIComponent(sort)}&search=${encodeURIComponent(search)}`;
            const res = await fetch(url);
            const plays = await res.json();

            if (!plays || plays.length === 0) {
                container.innerHTML = '<p style="grid-column: 1/-1; text-align:center;">कोई नाटक नहीं मिला।</p>';
                return;
            }

            container.innerHTML = plays.map(pl => {
                const dateObj = new Date(pl.created_at);
                const dateStr = !isNaN(dateObj) ? `${dateObj.getDate()}/${dateObj.getMonth() + 1}/${dateObj.getFullYear()}` : '';
                return `
                <article class="post-card">
                    <div class="post-image">
                        <img src="${pl.image_url || 'https://picsum.photos/600/400'}" alt="${pl.title}" loading="lazy">
                    </div>
                    <div class="post-content">
                        <h3><a href="play_detail.php?id=${pl.id}">${pl.title}</a></h3>
                        <p class="post-meta">नाटक • ${dateStr}</p>
                        <p>${pl.excerpt}</p>
                        <a href="play_detail.php?id=${pl.id}" class="read-more">पूरा पढ़ें</a>
                    </div>
                </article>
                `;
            }).join('');
        } catch (err) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align:center;">नाटक लोड करने में समस्या आई।</p>';
        }
    }

    document.getElementById('category-filter').addEventListener('change', loadPlays);
    document.getElementById('sort-by').addEventListener('change', loadPlays);
    document.getElementById('search-button').addEventListener('click', loadPlays);
    document.getElementById('plays-search').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') loadPlays();
    });

    document.addEventListener('DOMContentLoaded', loadPlays);
    </script>
</body>
</html>
