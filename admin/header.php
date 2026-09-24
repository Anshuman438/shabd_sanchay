<?php
// admin/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_admin_auth();

$current_page = basename($_SERVER['PHP_SELF']);

// Get quick badge counts
$badge_comments = 0;
$c_res = $conn->query("SELECT COUNT(*) as cnt FROM comments WHERE status = 'pending'");
if ($c_res) $badge_comments = $c_res->fetch_assoc()['cnt'];

$badge_submissions = 0;
$s_res = $conn->query("SELECT COUNT(*) as cnt FROM user_submissions WHERE status = 'pending'");
if ($s_res) $badge_submissions = $s_res->fetch_assoc()['cnt'];

$admin_name = $_SESSION['admin_name'] ?? 'व्यवस्थापक';
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'प्रशासन डैशबोर्ड' ?> - शब्द संचय</title>
    <link href="https://fonts.googleapis.com/css2?family=Biryani:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script>
        (function() {
            var theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        :root {
            --admin-sidebar-w: 260px;
            --admin-primary: #1e3a8a;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-text: #0f172a;
            --admin-text-muted: #64748b;
            --admin-border: #e2e8f0;
            --admin-table-th-bg: #f8fafc;
            --admin-table-hover: #f1f5f9;
        }

        [data-theme="dark"] {
            --admin-bg: #0f172a;
            --admin-card-bg: #1e293b;
            --admin-text: #f8fafc;
            --admin-text-muted: #94a3b8;
            --admin-border: #334155;
            --admin-table-th-bg: #141f32;
            --admin-table-hover: #263347;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: var(--admin-bg);
            color: var(--admin-text);
            font-family: 'Plus Jakarta Sans', 'Noto Sans Devanagari', sans-serif;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .admin-sidebar {
            width: var(--admin-sidebar-w);
            background: var(--admin-card-bg);
            border-right: 1px solid var(--admin-border);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid var(--admin-border);
        }
        .sidebar-brand h2 {
            font-family: 'Biryani', 'Noto Sans Devanagari', sans-serif;
            font-weight: 900;
            margin: 0;
            font-size: 20px;
            color: #d97706;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        [data-theme="dark"] .sidebar-brand h2 {
            color: #f59e0b;
        }
        .sidebar-brand p {
            margin: 4px 0 0 0;
            font-size: 13px;
            color: var(--admin-text-muted);
        }
        .sidebar-nav {
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 8px;
            color: var(--admin-text);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .sidebar-link .link-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-link:hover {
            background: var(--admin-table-hover);
            color: var(--admin-text);
        }
        .sidebar-link.active {
            background: #2563eb;
            color: #ffffff;
        }
        .sidebar-link .badge {
            background: #ef4444;
            color: white;
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow-x: hidden;
        }
        .admin-topbar {
            background: var(--admin-card-bg);
            border-bottom: 1px solid var(--admin-border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .admin-content {
            padding: 28px;
            flex: 1;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--admin-card-bg);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        .stat-card:hover {
            border-color: #2563eb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .stat-card .num {
            font-size: 28px;
            font-weight: 700;
            margin-top: 4px;
            color: #2563eb;
        }
        .admin-table-container {
            background: var(--admin-card-bg);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 24px;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }
        .admin-table th {
            background: var(--admin-table-th-bg);
            padding: 14px 16px;
            border-bottom: 1px solid var(--admin-border);
            font-weight: 600;
            color: var(--admin-text-muted);
        }
        .admin-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--admin-border);
            vertical-align: middle;
            color: var(--admin-text);
        }
        .admin-table tr:hover {
            background: var(--admin-table-hover);
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.18s ease;
        }
        .btn-edit { background: #e0f2fe; color: #0369a1; }
        .btn-edit:hover { background: #bae6fd; }
        [data-theme="dark"] .btn-edit { background: rgba(3, 105, 161, 0.25); color: #7dd3fc; border: 1px solid rgba(125, 211, 252, 0.2); }
        [data-theme="dark"] .btn-edit:hover { background: rgba(3, 105, 161, 0.4); }

        .btn-delete { background: #fee2e2; color: #b91c1c; }
        .btn-delete:hover { background: #fecaca; }
        [data-theme="dark"] .btn-delete { background: rgba(185, 28, 28, 0.25); color: #fca5a5; border: 1px solid rgba(252, 165, 165, 0.2); }
        [data-theme="dark"] .btn-delete:hover { background: rgba(185, 28, 28, 0.4); }

        .btn-approve { background: #dcfce7; color: #15803d; }
        .btn-approve:hover { background: #bbf7d0; }
        [data-theme="dark"] .btn-approve { background: rgba(21, 128, 61, 0.25); color: #86efac; border: 1px solid rgba(134, 239, 172, 0.2); }
        [data-theme="dark"] .btn-approve:hover { background: rgba(21, 128, 61, 0.4); }

        .btn-secondary { background: #e2e8f0; color: #334155; }
        .btn-secondary:hover { background: #cbd5e1; }
        [data-theme="dark"] .btn-secondary { background: #334155; color: #f8fafc; border: 1px solid #475569; }
        [data-theme="dark"] .btn-secondary:hover { background: #475569; }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        [data-theme="dark"] .alert-success { background: rgba(22, 101, 52, 0.3); color: #86efac; border-color: rgba(134, 239, 172, 0.3); }

        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        [data-theme="dark"] .alert-danger { background: rgba(153, 27, 27, 0.3); color: #fca5a5; border-color: rgba(252, 165, 165, 0.3); }

        .form-card {
            background: var(--admin-card-bg);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 14px;
            color: var(--admin-text);
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            background: var(--admin-bg);
            color: var(--admin-text);
            box-sizing: border-box;
            transition: border-color 0.2s, background 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }

        /* Sun / Moon theme button inside Admin header */
        .theme-toggle {
            color: var(--admin-text);
        }
        .theme-toggle .sun {
            display: none;
        }
        .theme-toggle .moon {
            display: inline-flex;
            color: #475569;
        }
        [data-theme="dark"] .theme-toggle .sun {
            display: inline-flex;
            color: #fbbf24;
        }
        [data-theme="dark"] .theme-toggle .moon {
            display: none;
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <h2>शब्द संचय</h2>
            <p>साहित्यिक प्रबंधन प्रणाली (CMS)</p>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="sidebar-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <span class="link-content">डैशबोर्ड</span>
            </a>
            <a href="manage_poems.php" class="sidebar-link <?= $current_page == 'manage_poems.php' ? 'active' : '' ?>">
                <span class="link-content">कविताएँ (Poems)</span>
            </a>
            <a href="manage_articles.php" class="sidebar-link <?= $current_page == 'manage_articles.php' ? 'active' : '' ?>">
                <span class="link-content">लेख (Articles)</span>
            </a>
            <a href="manage_stories.php" class="sidebar-link <?= $current_page == 'manage_stories.php' ? 'active' : '' ?>">
                <span class="link-content">कहानियाँ (Stories)</span>
            </a>
            <a href="manage_plays.php" class="sidebar-link <?= $current_page == 'manage_plays.php' ? 'active' : '' ?>">
                <span class="link-content">नाटक (Plays)</span>
            </a>
            <a href="manage_submissions.php" class="sidebar-link <?= $current_page == 'manage_submissions.php' ? 'active' : '' ?>">
                <span class="link-content">रचना प्रस्ताव (Submissions)</span>
                <?php if ($badge_submissions > 0): ?>
                    <span class="badge" style="background: #ea580c;"><?= $badge_submissions ?></span>
                <?php endif; ?>
            </a>
            <a href="manage_comments.php" class="sidebar-link <?= $current_page == 'manage_comments.php' ? 'active' : '' ?>">
                <span class="link-content">टिप्पणियाँ</span>
                <?php if ($badge_comments > 0): ?>
                    <span class="badge"><?= $badge_comments ?></span>
                <?php endif; ?>
            </a>
            <a href="manage_contacts.php" class="sidebar-link <?= $current_page == 'manage_contacts.php' ? 'active' : '' ?>">
                <span class="link-content">संपर्क संदेश</span>
            </a>
            <a href="manage_subscribers.php" class="sidebar-link <?= $current_page == 'manage_subscribers.php' ? 'active' : '' ?>">
                <span class="link-content">न्यूज़लेटर ग्राहक</span>
            </a>
            <a href="manage_about.php" class="sidebar-link <?= $current_page == 'manage_about.php' ? 'active' : '' ?>">
                <span class="link-content">हमारे बारे में (About Us)</span>
            </a>
            <hr style="margin: 16px 0; border: 0; border-top: 1px solid var(--admin-border);">
            <a href="../index.php" target="_blank" class="sidebar-link">
                <span class="link-content">मुख्य वेबसाइट देखें</span>
            </a>
            <a href="logout.php" class="sidebar-link" style="color: #ef4444;">
                <span class="link-content">लॉगआउट (Logout)</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <header class="admin-topbar">
            <div>
                <strong>नमस्ते, <?= htmlspecialchars($admin_name) ?></strong>
                <span style="font-size: 12px; color: #64748b; margin-left: 8px;">(<?= htmlspecialchars($_SESSION['admin_role'] ?? 'admin') ?>)</span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button class="theme-toggle" aria-label="Toggle Theme" style="border: 1px solid var(--admin-border); padding: 6px 12px; border-radius: 8px; cursor: pointer; background: transparent; display: flex; align-items: center;">
                    <span class="sun"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg></span><span class="moon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg></span>
                </button>
                <a href="logout.php" class="btn-action btn-delete">लॉगआउट</a>
            </div>
        </header>
        <main class="admin-content">
