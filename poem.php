<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: poetry.php");
    exit;
}

$poem_id = intval($_GET['id']);
error_log("Fetching poem with ID: " . $poem_id);

// Fetch poem data
$stmt = $conn->prepare("SELECT * FROM poems WHERE id = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header("Location: poetry.php");
    exit;
}
$stmt->bind_param("i", $poem_id);
$stmt->execute();
$result = $stmt->get_result();
$poem = $result->fetch_assoc();

if (!$poem) {
    error_log("No poem found with ID: " . $poem_id);
    header("Location: poetry.php");
    exit;
}

// Increment view count
$conn->query("UPDATE poems SET views = views + 1 WHERE id = $poem_id");

$page_title = $poem['title'] . " - हिंदी साहित्य";
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
     <!-- Page Loader -->
     <div id="page-loader" aria-label="Loading" role="status" aria-live="polite">
        <div class="loader-spinner"></div>
    </div>
  
    <?php include 'header.php'; ?>

    <main class="container">
        <article class="single-post">
            <header class="post-header">
                <div class="post-category">कविता</div>
                <h1 class="post-title"><?php echo htmlspecialchars($poem['title']); ?></h1>
                <div class="post-meta">
                    <span class="post-author"><?php echo htmlspecialchars($poem['author_name']); ?></span>
                    <span class="post-date"><?php echo date('d M Y', strtotime($poem['created_at'])); ?></span>
                </div>
            </header>

            <?php if (!empty($poem['image_url'])): ?>
               
                    <div class="post-image">
                        <img src="<?php echo htmlspecialchars($poem['image_url']); ?>" alt="<?php echo htmlspecialchars($poem['title']); ?>">
                    </div>
                
            <?php endif; ?>

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($poem['content'])); ?>
            </div>

            <div class="footer-single">
            <footer class="post-footer">
                <div class="post-actions">
                    <button class="like-button" onclick="likePoem(<?php echo $poem['id']; ?>)">
                        ❤️ <span id="like-count"><?php echo $poem['likes']; ?></span>
                    </button>
                    <button class="share-button" onclick="toggleShareOptions()">शेयर करें</button>
                    <div class="share-options" id="share-options" style="display: none;">
                        <a href="#" onclick="shareOnFacebook()">📘</a>
                        <a href="#" onclick="shareOnTwitter()">🐦</a>
                        <a href="#" onclick="shareOnWhatsApp()">📱</a>
                    </div>
                </div>
                <div class="post-tags">
                    <a href="poetry.php?category=<?php echo urlencode($poem['category']); ?>">
                        <?php echo htmlspecialchars($poem['category'] ?: 'सामान्य'); ?>
                    </a>
                    <span class="read-time">⏱️ <?php echo $poem['read_time'] ?? 3; ?> मिनट पढ़ने</span>
                </div>
            </footer>
            </div>
        </article>

        <section class="related-posts">
            <h2>संबंधित कविताएँ</h2>
            <div class="related-posts-grid" id="related-poems"></div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script>

        // Add this script to handle page loading
    document.addEventListener('DOMContentLoaded', function() {
        // Hide loader when everything is loaded
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
            setTimeout(function() {
                document.getElementById('page-loader').style.display = 'none';
            }, 500); // Match this with the CSS transition time
        });
        
        // In case load event doesn't fire, add a fallback
        setTimeout(function() {
            document.body.classList.add('loaded');
            document.getElementById('page-loader').style.display = 'none';
        }, 3000); // 3 seconds maximum wait time
        
        });


    // Like poem functionality
    async function likePoem(poemId) {
        try {
            const response = await fetch(`api/like_poem.php?id=${poemId}`);
            const result = await response.json();
            
            if (result.success) {
                document.getElementById('like-count').textContent = result.newLikes;
                document.querySelector('.like-button').classList.add('liked');
            }
        } catch (error) {
            console.error('Error liking poem:', error);
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

    // Load related poems
    async function loadRelatedPoems(poemId, category) {
        try {
            const response = await fetch(`api/get_related_poems.php?id=${poemId}&category=${encodeURIComponent(category)}`);
            const poems = await response.json();

            const container = document.getElementById('related-poems');
            if (poems.length > 0) {
                container.innerHTML = poems.slice(0, 3).map(poem => `
                    <article class="related-post-card">
                        <h3><a href="poem.php?id=${poem.id}">${poem.title}</a></h3>
                       <p>${poem.content.split('\n').slice(0, 2).map(line => line.trim()).join('<br>')}</p>
                    </article>
                `).join('');
            } else {
                container.innerHTML = '<p>कोई संबंधित कविताएँ उपलब्ध नहीं हैं</p>';
            }
        } catch (error) {
            console.error('Error loading related poems:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadRelatedPoems(<?php echo $poem_id; ?>, "<?php echo addslashes($poem['category'] ?? ''); ?>");
    });
    </script>
    <script src="js/theme.js"></script>
</body>
</html>
