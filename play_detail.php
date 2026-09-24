<?php
// play_detail.php
require_once 'config.php';
require_once 'includes/helpers.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: play.php');
    exit();
}

// Increment views
$conn->query("UPDATE plays SET views = views + 1 WHERE id = $id");

// Fetch play details
$stmt = $conn->prepare("SELECT * FROM plays WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$play = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$play) {
    header('Location: play.php');
    exit();
}

// Fetch related plays
$rel_stmt = $conn->prepare("SELECT id, title, author_name, image_url, category, created_at, excerpt FROM plays WHERE id != ? ORDER BY likes DESC LIMIT 3");
$rel_stmt->bind_param("i", $id);
$rel_stmt->execute();
$related = $rel_stmt->get_result();

$page_title = $play['title'] . " - हिंदी नाटक";
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($play['title']) ?> - शब्द संचय</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Inter:wght@400;500;700&family=Noto+Sans+Devanagari:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container">
        <article class="single-post">
            <header class="post-header">
                <div class="post-category">नाटक</div>
                <h1 class="post-title"><?= htmlspecialchars($play['title']) ?></h1>
                <div class="post-meta">
                    <span class="post-author"><?= htmlspecialchars($play['author_name']) ?></span>
                    <span class="post-date"><?= date('d/m/Y', strtotime($play['created_at'])) ?></span>
                </div>
            </header>

            <div class="post-content" id="play-content">
                <?= nl2br(htmlspecialchars($play['content'])) ?>
            </div>

            <footer class="post-footer">
                <div class="post-tags">
                    <a href="play.php?category=<?= urlencode($play['category']) ?>">
                        <?= htmlspecialchars($play['category'] ?: 'नाटक') ?>
                    </a>
                </div>
                <div class="post-actions">
                    <button class="like-button" id="like-btn" data-id="<?= $play['id'] ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align: -2px; margin-right: 4px;"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg><span id="like-count"><?= $play['likes'] ?></span>
                    </button>
                    <button class="share-button" id="copy-btn">शेयर करें</button>
                </div>
            </footer>

            <div class="author-box">
                <div class="author-image">
                    <img src="images/authors/author-default.jpg" alt="लेखक" loading="lazy">
                </div>
                <div class="author-info">
                    <h3><?= htmlspecialchars($play['author_name']) ?></h3>
                    <p>हिंदी साहित्य और नाट्य विधा के रचयिता।</p>
                    <a href="play.php?author=<?= urlencode($play['author_name']) ?>" class="author-link">
                        और रचनाएँ पढ़ें
                    </a>
                </div>
            </div>

            <section class="comments-section">
                <h2>टिप्पणियाँ</h2>
                <div class="comment-form">
                    <h3>अपनी टिप्पणी जोड़ें</h3>
                    <form id="comment-form">
                        <input type="hidden" name="content_id" value="<?= $play['id'] ?>">
                        <input type="hidden" name="content_type" value="play">
                        <div class="form-group">
                            <label for="comment-name">नाम</label>
                            <input type="text" id="comment-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="comment-email">ईमेल</label>
                            <input type="email" id="comment-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="comment-text">टिप्पणी</label>
                            <textarea id="comment-text" name="comment" required></textarea>
                        </div>
                        <button type="submit" class="submit-button">सबमिट करें</button>
                        <div id="comment-msg" style="margin-top: 10px; font-size: 14px; display: none;"></div>
                    </form>
                </div>

                <div class="comments-list" id="comments-list">
                    <!-- Comments loaded via JavaScript -->
                </div>
            </section>

            <!-- Related Plays -->
            <?php if ($related && $related->num_rows > 0): ?>
                <section class="related-posts">
                    <h2>संबंधित नाटक</h2>
                    <div class="related-posts-grid">
                        <?php while ($r = $related->fetch_assoc()): ?>
                            <article class="related-post-card">
                                <div class="related-post-image">
                                    <img src="<?= $r['image_url'] || 'https://picsum.photos/600/400' ?>" alt="<?= htmlspecialchars($r['title']) ?>" loading="lazy">
                                </div>
                                <div class="related-post-content">
                                    <h3><a href="play_detail.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></h3>
                                    <p class="post-meta"><?= date('d/m/Y', strtotime($r['created_at'])) ?></p>
                                    <p><?= htmlspecialchars(make_excerpt($r['excerpt'], 100)) ?></p>
                                    <a href="play_detail.php?id=<?= $r['id'] ?>" class="read-more">पढ़ें</a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php endif; ?>
        </article>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    // Copy Link
    document.getElementById('copy-btn')?.addEventListener('click', () => {
        navigator.clipboard.writeText(window.location.href);
        const btn = document.getElementById('copy-btn');
        btn.textContent = '✓ कॉपी हो गया!';
        setTimeout(() => { btn.textContent = 'शेयर करें'; }, 2000);
    });

    // Like / Unlike AJAX
    const playId = <?= $play['id'] ?>;
    const likeBtn = document.getElementById('like-btn');
    let hasLikedPlay = localStorage.getItem('play_liked_' + playId) === 'true';
    if (hasLikedPlay && likeBtn) {
        likeBtn.classList.add('liked');
        likeBtn.style.color = '#e11d48';
    }

    likeBtn?.addEventListener('click', async function() {
        const action = hasLikedPlay ? 'unlike' : 'like';
        try {
            const res = await fetch(`api/like_play.php?id=${playId}&action=${action}`);
            const data = await res.json();
            if (data.success && data.data && data.data.newLikes !== undefined) {
                document.getElementById('like-count').textContent = data.data.newLikes;
                if (action === 'like') {
                    likeBtn.classList.add('liked');
                    likeBtn.style.color = '#e11d48';
                    localStorage.setItem('play_liked_' + playId, 'true');
                    hasLikedPlay = true;
                } else {
                    likeBtn.classList.remove('liked');
                    likeBtn.style.color = '';
                    localStorage.removeItem('play_liked_' + playId);
                    hasLikedPlay = false;
                }
            }
        } catch (e) {
            console.error('Error toggling play like:', e);
        }
    });

    // Load Comments
    async function loadComments() {
        const list = document.getElementById('comments-list');
        try {
            const res = await fetch(`api/get_comments.php?id=<?= $play['id'] ?>&type=play`);
            const comments = await res.json();
            if (!comments || comments.length === 0) {
                list.innerHTML = '<p>अभी कोई टिप्पणी नहीं है। पहली टिप्पणी जोड़ें!</p>';
                return;
            }
            list.innerHTML = comments.map(c => `
                <div class="comment-item">
                    <div class="comment-header">
                        <span class="comment-author">${c.name}</span>
                        <span class="comment-date">${c.formatted_date || ''}</span>
                    </div>
                    <div class="comment-content">
                        <p>${c.comment}</p>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            list.innerHTML = '<p>टिप्पणियाँ लोड करने में समस्या आई।</p>';
        }
    }

    // Submit Comment
    document.getElementById('comment-form')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const msg = document.getElementById('comment-msg');
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        try {
            const res = await fetch('api/add_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            msg.style.display = 'block';
            if (data.success) {
                msg.style.color = '#15803d';
                msg.textContent = '✓ ' + data.message;
                form.reset();
                loadComments();
            } else {
                msg.style.color = '#b91c1c';
                msg.textContent = '✗ ' + data.message;
            }
        } catch (err) {
            msg.style.display = 'block';
            msg.style.color = '#b91c1c';
            msg.textContent = '✗ नेटवर्क त्रुटि, कृपया पुनः प्रयास करें।';
        }
    });

    document.addEventListener('DOMContentLoaded', loadComments);
    </script>
</body>
</html>
