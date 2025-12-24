<?php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: articles.php");
    exit;
}

$article_id = intval($_GET['id']);
error_log("Fetching article with ID: " . $article_id);

$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header("Location: articles.php");
    exit;
}

$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    error_log("No article found with ID: " . $article_id);
    header("Location: articles.php");
    exit;
}

// Increment view count
$conn->query("UPDATE articles SET views = views + 1 WHERE id = $article_id");

$page_title = $article['title'] . " - हिंदी साहित्य";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <article class="single-post">
            <header class="post-header">
                <div class="post-category">लेख</div>
                <h1 class="post-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                <div class="post-meta">
                    <span class="post-author"><?php echo htmlspecialchars($article['author_name']); ?></span>
                    <span class="post-date"><?php echo date('d M Y', strtotime($article['created_at'])); ?></span>
                </div>
            </header>

            <?php if ($article['image_url']): ?>
            <div class="post-image">
                <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
            </div>
            <?php endif; ?>

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($article['content'])); ?>
            </div>

            <div class="footer-single">
            <footer class="post-footer">
                <div class="post-actions">
                    <button class="like-button" onclick="likeArticle(<?php echo $article['id']; ?>)">
                        ❤️ <span id="like-count"><?php echo $article['likes']; ?></span>
                    </button>
                    <button class="share-button" onclick="toggleShareOptions()">शेयर करें</button>
                    <div class="share-options" id="share-options" style="display: none;">
                        <a href="#" onclick="shareOnFacebook()">📘</a>
                        <a href="#" onclick="shareOnTwitter()">🐦</a>
                        <a href="#" onclick="shareOnWhatsApp()">📱</a>
                    </div>
                </div>
                <div class="post-tags">
                    <a href="articles.php?category=<?php echo urlencode($article['category']); ?>">
                        <?php echo htmlspecialchars($article['category'] ?: 'सामान्य'); ?>
                    </a>
                    <span class="read-time">⏱️ <?php echo $article['read_time'] ?? 5; ?> मिनट पढ़ने</span>
                </div>
            </footer>
            </div>
        </article>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    // Like article functionality
    async function likeArticle(articleId) {
        try {
            const response = await fetch(`api/like_article.php?id=${articleId}`);
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('like-count').textContent = result.newLikes;
                document.querySelector('.like-button').classList.add('liked');
            }
        } catch (error) {
            console.error('Error liking article:', error);
        }
    }

    // Share functionality
    function toggleShareOptions() {
        const options = document.getElementById('share-options');
        options.style.display = options.style.display === 'none' ? 'flex' : 'none';
    }

    function shareOnFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
    }

    function shareOnTwitter() {
        const text = encodeURIComponent(`"${document.title}" - हिंदी साहित्य`);
        const url = encodeURIComponent(window.location.href);
        window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
    }

    function shareOnWhatsApp() {
        const text = encodeURIComponent(`"${document.title}" - हिंदी साहित्य\n${window.location.href}`);
        window.open(`https://wa.me/?text=${text}`, '_blank');
    }
    </script>
    <script src="js/theme.js"></script>
</body>
</html>