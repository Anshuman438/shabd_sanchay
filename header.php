<?php
// header.php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<script>
    // Immediate theme initialization to prevent flash
    (function() {
        var theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
    })();
</script>
<header class="site-header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php" class="logo-link">
                <span class="logo-feather-icon">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path>
                        <line x1="16" y1="8" x2="2" y2="22"></line>
                        <line x1="17.5" y1="15" x2="9" y2="15"></line>
                    </svg>
                </span>
                <div class="logo-text-group">
                    <h1 class="logo-brand-title">शब्द संचय</h1>
                    <span class="logo-brand-subtitle">विचारों के नए प्रतिमान</span>
                </div>
            </a>
        </div>

        <nav class="main-nav" id="main-nav">
            <div class="mobile-nav-search-container">
                <form action="search.php" method="GET" class="mobile-nav-search-form">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="q" placeholder="शब्द संचय पर खोजें..." aria-label="Search content">
                </form>
            </div>
            <ul>
                <li><a href="index.php" <?= $current_page == 'index.php' ? 'class="active"' : '' ?>>होम</a></li>
                <li><a href="poetry.php" <?= in_array($current_page, ['poetry.php', 'poem.php']) ? 'class="active"' : '' ?>>कविताएँ</a></li>
                <li><a href="articles.php" <?= in_array($current_page, ['articles.php', 'article.php']) ? 'class="active"' : '' ?>>लेख</a></li>
                <li><a href="stories.php" <?= in_array($current_page, ['stories.php', 'story.php']) ? 'class="active"' : '' ?>>कहानियाँ</a></li>
                <li><a href="learn.php" <?= in_array($current_page, ['learn.php', 'craft_guide.php']) ? 'class="active"' : '' ?>>सीखें</a></li>
                <li><a href="submit.php" <?= $current_page == 'submit.php' ? 'class="active"' : '' ?>>रचना भेजें</a></li>
                <li><a href="about.php" <?= $current_page == 'about.php' ? 'class="active"' : '' ?>>हमारे बारे में</a></li>
            </ul>
        </nav>

        <div class="header-right-actions">
            <div class="header-search-box desktop-search-only">
                <form action="search.php" method="GET" class="nav-search-form">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="q" placeholder="खोजें..." aria-label="Search content">
                </form>
            </div>

            <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">
                <span class="sun"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg></span>
                <span class="moon"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg></span>
            </button>

            <?php if (!empty($_SESSION['admin_logged_in'])): ?>
                <a href="admin/dashboard.php" class="btn-header-login" style="background: #1e3a8a; color: white;">व्यवस्थापक</a>
            <?php elseif (!empty($_SESSION['user_logged_in'])): ?>
                <a href="submit.php" class="btn-header-login" title="रचनाकार प्रोफ़ाइल (<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>)" style="display: inline-flex; align-items: center; gap: 6px; padding: 0 0.75rem;">
                    <img src="<?= htmlspecialchars($_SESSION['user_photo'] ?? 'images/authors/author-default.jpg') ?>" alt="User" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; border: 1.5px solid rgba(255,255,255,0.7);">
                    <span class="header-user-name-text" style="font-size: 13px; font-weight: bold; max-width: 65px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars(mb_substr($_SESSION['user_name'] ?? 'रचनाकार', 0, 8)) ?></span>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-header-login">लॉग इन</a>
            <?php endif; ?>

            <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Open Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<!-- Page Shift Loader Overlay -->
<div id="page-shift-loader" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: var(--bg, #fdfbf7);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.2s ease, visibility 0.2s ease;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
">
    <div style="
        width: 28px;
        height: 28px;
        border: 3px solid rgba(37, 99, 235, 0.18);
        border-top: 3px solid #2563eb;
        border-radius: 50%;
        animation: pageSpin 0.65s linear infinite;
    "></div>
</div>

<style>
@keyframes pageSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
[data-theme="dark"] #page-shift-loader {
    background-color: #16120e !important;
}
</style>

<script>
(function() {
    function initLoader() {
        const loader = document.getElementById('page-shift-loader');
        if (!loader) return;

        loader.style.opacity = '0';
        loader.style.visibility = 'hidden';

        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && !link.target && !link.hasAttribute('download')) {
                const url = new URL(link.href, window.location.href);
                if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                    loader.style.visibility = 'visible';
                    loader.style.opacity = '1';
                }
            }
        });

        window.addEventListener('beforeunload', function() {
            if (loader) {
                loader.style.visibility = 'visible';
                loader.style.opacity = '1';
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLoader);
    } else {
        initLoader();
    }
})();
</script>