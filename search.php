<?php
require_once 'config.php';
require_once 'includes/helpers.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_type = isset($_GET['type']) ? trim($_GET['type']) : 'all';

$page_title = (!empty($search_query) ? htmlspecialchars($search_query) . " - " : "") . "खोज परिणाम | शब्द संचय";

$results = [
    'poems' => [],
    'articles' => [],
    'stories' => []
];

if (!empty($search_query)) {
    $param = "%{$search_query}%";

    // 1. Search Poems
    $stmt = $conn->prepare("SELECT id, title, author_name, category, content, views, likes, created_at, 'poem' as content_type FROM poems WHERE title LIKE ? OR author_name LIKE ? OR category LIKE ? OR content LIKE ? ORDER BY views DESC LIMIT 30");
    $stmt->bind_param("ssss", $param, $param, $param, $param);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $results['poems'][] = $row;
    }
    $stmt->close();

    // 2. Search Articles
    $stmt = $conn->prepare("SELECT id, title, author_name, category, excerpt, content, views, likes, created_at, 'article' as content_type FROM articles WHERE title LIKE ? OR author_name LIKE ? OR category LIKE ? OR content LIKE ? ORDER BY views DESC LIMIT 30");
    $stmt->bind_param("ssss", $param, $param, $param, $param);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $results['articles'][] = $row;
    }
    $stmt->close();

    // 3. Search Stories
    $stmt = $conn->prepare("SELECT id, title, author_name, category, excerpt, content, views, likes, created_at, 'story' as content_type FROM stories WHERE title LIKE ? OR author_name LIKE ? OR category LIKE ? OR content LIKE ? ORDER BY views DESC LIMIT 30");
    $stmt->bind_param("ssss", $param, $param, $param, $param);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $results['stories'][] = $row;
    }
    $stmt->close();
}

$total_count = count($results['poems']) + count($results['articles']) + count($results['stories']);
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Noto+Serif+Devanagari:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Rozha+One&family=Yatra+One&display=swap" rel="stylesheet">
</head>
<body class="search-results-page">
    <?php include 'header.php'; ?>

    <main class="main-content">
        <div class="poetry-page-wrapper">
            <!-- Search Hero Banner -->
            <section class="poetry-page-hero">
                <div class="container">
                    <div class="poetry-hero-content">
                        <span class="page-eyebrow">ज्ञान एवं साहित्य खोज • SEARCH PORTAL</span>
                        <h1 class="page-main-title">साहित्यिक खोज</h1>
                        <div class="heading-artistic-underline"></div>
                        <p class="page-lead-subtitle">कविताएँ, लेख, कहानियाँ, विचार और अपने प्रिय रचनाकारों को खोजें।</p>

                        <!-- Search Box Form -->
                        <div class="search-portal-form-wrap">
                            <form action="search.php" method="GET" class="search-portal-form" id="search-portal-form">
                                <div class="search-portal-icon-wrap">
                                    <svg class="search-icon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </div>
                                <input type="text" name="q" id="search-portal-input" class="search-portal-input" placeholder="शीर्षक, रचनाकार, पंक्ति या विषय खोजें..." value="<?= htmlspecialchars($search_query) ?>" autofocus autocomplete="off">
                                <?php if (!empty($search_query)): ?>
                                    <a href="search.php" class="search-portal-clear-btn" id="search-portal-clear-btn" title="खोज साफ़ करें">✕</a>
                                <?php endif; ?>
                                <?php if (!empty($search_type) && $search_type !== 'all'): ?>
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($search_type) ?>">
                                <?php endif; ?>
                                <button type="submit" class="search-portal-btn">
                                    <span>खोजें</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </button>
                            </form>
                        </div>

                        <!-- Type Filters -->
                        <?php if (!empty($search_query)): ?>
                            <div class="category-pills-bar" style="margin-top: 1.5rem;">
                                <a href="search.php?q=<?= urlencode($search_query) ?>&type=all" class="category-pill <?= $search_type === 'all' ? 'active' : '' ?>">
                                    <span>सभी परिणाम (<?= $total_count ?>)</span>
                                </a>
                                <a href="search.php?q=<?= urlencode($search_query) ?>&type=poems" class="category-pill <?= $search_type === 'poems' ? 'active' : '' ?>">
                                    <span>कविताएँ (<?= count($results['poems']) ?>)</span>
                                </a>
                                <a href="search.php?q=<?= urlencode($search_query) ?>&type=articles" class="category-pill <?= $search_type === 'articles' ? 'active' : '' ?>">
                                    <span>लेख (<?= count($results['articles']) ?>)</span>
                                </a>
                                <a href="search.php?q=<?= urlencode($search_query) ?>&type=stories" class="category-pill <?= $search_type === 'stories' ? 'active' : '' ?>">
                                    <span>कहानियाँ (<?= count($results['stories']) ?>)</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Results Section -->
            <section class="poetry-content-section">
                <div class="container">
                    <?php if (empty($search_query)): ?>
                        <!-- Initial state suggestions -->
                        <div class="poetry-empty-state">
                            <div class="empty-state-title">खोजने के लिए कुछ लिखें</div>
                            <p class="empty-state-text">आप ‘दिनकर’, ‘मधुशाला’, ‘प्रकृति’, ‘प्रेम’, ‘निराला’, या किसी भी विषय पर खोज सकते हैं।</p>
                            <div class="popular-search-tags" style="display:flex; justify-content:center; gap:0.6rem; flex-wrap:wrap; margin-top:1.2rem;">
                                <a href="search.php?q=दिनकर" class="category-pill">दिनकर</a>
                                <a href="search.php?q=प्रकृति" class="category-pill">प्रकृति</a>
                                <a href="search.php?q=प्रेम" class="category-pill">प्रेम</a>
                                <a href="search.php?q=महादेवी" class="category-pill">महादेवी वर्मा</a>
                                <a href="search.php?q=बच्चन" class="category-pill">हरिवंश राय बच्चन</a>
                                <a href="search.php?q=संवेदना" class="category-pill">संवेदना</a>
                            </div>
                        </div>
                    <?php elseif ($total_count === 0): ?>
                        <div class="poetry-empty-state">
                            <div class="empty-state-title">"<?= htmlspecialchars($search_query) ?>" के लिए कोई परिणाम नहीं मिला</div>
                            <p class="empty-state-text">कृपया अलग कीवर्ड आज़माएँ या वर्तनी की जाँच करें।</p>
                            <a href="search.php" class="btn-reset-filters" style="display:inline-block; margin-top:1rem; text-decoration:none;">पुनः खोजें</a>
                        </div>
                    <?php else: ?>
                        <!-- Show results -->
                        <?php 
                        $items_to_show = [];
                        if ($search_type === 'all' || $search_type === 'poems') {
                            foreach ($results['poems'] as $p) $items_to_show[] = $p;
                        }
                        if ($search_type === 'all' || $search_type === 'articles') {
                            foreach ($results['articles'] as $a) $items_to_show[] = $a;
                        }
                        if ($search_type === 'all' || $search_type === 'stories') {
                            foreach ($results['stories'] as $s) $items_to_show[] = $s;
                        }
                        ?>

                        <div class="results-header-info" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                            <span class="results-count-text" style="font-family: var(--font-body); font-weight: 600; color: var(--color-ink-light);">
                                "<strong><?= htmlspecialchars($search_query) ?></strong>" के लिए कुल <?= count($items_to_show) ?> परिणाम प्राप्त हुए
                            </span>
                        </div>

                        <div class="poetry-grid">
                            <?php foreach ($items_to_show as $item): 
                                $type_label = $item['content_type'] === 'poem' ? 'कविता' : ($item['content_type'] === 'article' ? 'लेख' : 'कहानी');
                                $link = $item['content_type'] === 'poem' ? "poem.php?id={$item['id']}" : ($item['content_type'] === 'article' ? "article.php?id={$item['id']}" : "story.php?id={$item['id']}");
                                $excerpt = !empty($item['excerpt']) ? $item['excerpt'] : mb_substr(str_replace(["\r", "\n"], ' ', $item['content']), 0, 140) . '...';
                            ?>
                                <article class="poetry-grid-card">
                                    <div class="card-top-row">
                                        <span class="card-category-tag"><?= $type_label ?> • <?= htmlspecialchars($item['category'] ?: 'सामान्य') ?></span>
                                        <div class="card-metrics">
                                            <span class="metric-item" title="देखा गया">
                                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                <span><?= $item['views'] ?></span>
                                            </span>
                                            <span class="metric-item" title="पसंद">
                                                <svg viewBox="0 0 24 24" width="13" height="13" fill="#e11d48" stroke="#e11d48" stroke-width="1"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                                <span><?= $item['likes'] ?></span>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="card-body-layout">
                                        <div class="card-main-content">
                                            <h2 class="card-poem-title">
                                                <a href="<?= $link ?>"><?= htmlspecialchars($item['title']) ?></a>
                                            </h2>
                                            <div class="card-poet-row">
                                                <span class="poet-feather-icon">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path><line x1="16" y1="8" x2="2" y2="22"></line><line x1="17.5" y1="15" x2="9" y2="15"></line></svg>
                                                </span>
                                                <span class="poet-name"><?= htmlspecialchars($item['author_name']) ?></span>
                                            </div>
                                            <div class="card-poem-excerpt">
                                                <p><?= htmlspecialchars($excerpt) ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer-row">
                                        <span class="card-date"><?= format_hindi_date($item['created_at']) ?></span>
                                        <a href="<?= $link ?>" class="btn-card-read-more">
                                            <span>पूरा पढ़ें</span>
                                            <span class="btn-arrow">→</span>
                                        </a>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
