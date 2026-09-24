<?php
// admin/dashboard.php
$page_title = "डैशबोर्ड";
require_once __DIR__ . '/header.php';

// Fetch metrics
$poems_cnt = $conn->query("SELECT COUNT(*) as cnt, IFNULL(SUM(views),0) as views, IFNULL(SUM(likes),0) as likes FROM poems")->fetch_assoc();
$articles_cnt = $conn->query("SELECT COUNT(*) as cnt, IFNULL(SUM(views),0) as views, IFNULL(SUM(likes),0) as likes FROM articles")->fetch_assoc();
$stories_cnt = $conn->query("SELECT COUNT(*) as cnt, IFNULL(SUM(views),0) as views, IFNULL(SUM(likes),0) as likes FROM stories")->fetch_assoc();
$plays_cnt = $conn->query("SELECT COUNT(*) as cnt, IFNULL(SUM(views),0) as views, IFNULL(SUM(likes),0) as likes FROM plays")->fetch_assoc();
$comments_cnt = $conn->query("SELECT COUNT(*) as cnt FROM comments")->fetch_assoc()['cnt'];
$contacts_cnt = $conn->query("SELECT COUNT(*) as cnt FROM contacts")->fetch_assoc()['cnt'];
$subscribers_cnt = $conn->query("SELECT COUNT(*) as cnt FROM newsletter_subscribers")->fetch_assoc()['cnt'];
$pending_subs_cnt = $conn->query("SELECT COUNT(*) as cnt FROM user_submissions WHERE status = 'pending'")->fetch_assoc()['cnt'] ?? 0;

$total_content = $poems_cnt['cnt'] + $articles_cnt['cnt'] + $stories_cnt['cnt'] + $plays_cnt['cnt'];
$total_views = $poems_cnt['views'] + $articles_cnt['views'] + $stories_cnt['views'] + $plays_cnt['views'];
$total_likes = $poems_cnt['likes'] + $articles_cnt['likes'] + $stories_cnt['likes'] + $plays_cnt['likes'];

// Recent submissions
$recent_submissions = $conn->query("SELECT * FROM user_submissions ORDER BY created_at DESC LIMIT 5");
// Recent comments
$recent_comments = $conn->query("SELECT * FROM comments ORDER BY created_at DESC LIMIT 5");
// Recent inquiries
$recent_contacts = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
?>

<div style="margin-bottom: 24px;">
    <h1 style="font-size: 24px; margin-bottom: 6px;">साहित्यिक पोर्टल अवलोकन (Dashboard Overview)</h1>
    <p style="color: #64748b; font-size: 14px;">शब्द संचय की वर्तमान सामग्री, पाठक सहभागिता और सांख्यिकी।</p>
</div>

<!-- Key Stat Cards -->
<div class="stats-grid">
    <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='manage_submissions.php'">
        <div>
            <div style="color: #64748b; font-size: 13px; font-weight: 600;">लंबित रचना प्रस्ताव (Submissions)</div>
            <div class="num" style="color: #ea580c;"><?= number_format($pending_subs_cnt) ?></div>
            <div style="font-size: 12px; color: #ea580c; margin-top: 4px; font-weight: 600;">समीक्षा करें व प्रकाशित करें →</div>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div style="color: #64748b; font-size: 13px; font-weight: 600;">कुल रचनाएँ (Content)</div>
            <div class="num" style="color: #2563eb;"><?= number_format($total_content) ?></div>
            <div style="font-size: 12px; color: #10b981; margin-top: 4px;">कविताएँ, लेख, नाटक व कहानियाँ</div>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div style="color: #64748b; font-size: 13px; font-weight: 600;">कुल पाठक दर्शन (Views)</div>
            <div class="num" style="color: #059669;"><?= number_format($total_views) ?></div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">लाइव वेब विज़िट्स</div>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div style="color: #64748b; font-size: 13px; font-weight: 600;">कुल पसंद (Likes)</div>
            <div class="num" style="color: #e11d48;"><?= number_format($total_likes) ?></div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">पाठक प्रशंसा</div>
        </div>
    </div>
</div>

<!-- Detailed Section Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 28px;">
    <!-- Breakdown -->
    <div class="form-card" style="margin-bottom: 0;">
        <h3 style="font-size: 16px; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">विधावार विवरण (Category Breakdown)</h3>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
            <li style="display: flex; justify-content: space-between; align-items: center;">
                <span><strong>कविताएँ (Poems)</strong></span>
                <span class="badge" style="background: #e0f2fe; color: #0369a1; padding: 4px 10px;"><?= $poems_cnt['cnt'] ?></span>
            </li>
            <li style="display: flex; justify-content: space-between; align-items: center;">
                <span><strong>साहित्यिक लेख (Articles)</strong></span>
                <span class="badge" style="background: #fef3c7; color: #92400e; padding: 4px 10px;"><?= $articles_cnt['cnt'] ?></span>
            </li>
            <li style="display: flex; justify-content: space-between; align-items: center;">
                <span><strong>कहानियाँ (Stories)</strong></span>
                <span class="badge" style="background: #dcfce7; color: #166534; padding: 4px 10px;"><?= $stories_cnt['cnt'] ?></span>
            </li>
            <li style="display: flex; justify-content: space-between; align-items: center;">
                <span><strong>नाटक व एकांकी (Plays)</strong></span>
                <span class="badge" style="background: #f3e8ff; color: #6b21a8; padding: 4px 10px;"><?= $plays_cnt['cnt'] ?></span>
            </li>
        </ul>
    </div>

    <!-- Quick Shortcuts -->
    <div class="form-card" style="margin-bottom: 0;">
        <h3 style="font-size: 16px; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">त्वरित क्रियाएँ (Quick Actions)</h3>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="manage_poems.php?action=create" class="btn-action btn-edit" style="justify-content: center; padding: 10px;">+ नई कविता जोड़ें (Add Poem)</a>
            <a href="manage_articles.php?action=create" class="btn-action btn-edit" style="justify-content: center; padding: 10px;">+ नया लेख जोड़ें (Add Article)</a>
            <a href="manage_stories.php?action=create" class="btn-action btn-edit" style="justify-content: center; padding: 10px;">+ नई कहानी जोड़ें (Add Story)</a>
            <a href="manage_plays.php?action=create" class="btn-action btn-edit" style="justify-content: center; padding: 10px;">+ नया नाटक जोड़ें (Add Play)</a>
        </div>
    </div>
</div>

<!-- Recent User Submissions (रचना प्रस्ताव) -->
<div class="admin-table-container" style="margin-bottom: 28px;">
    <div style="padding: 16px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--admin-border);">
        <span style="color: #ea580c;">✨ हालिया रचना प्रस्ताव (Recent User Submissions)</span>
        <a href="manage_submissions.php" style="font-size: 13px; color: #ea580c; text-decoration: none; font-weight: 700;">सभी प्रस्ताव देखें व प्रकाशित करें (<?= $pending_subs_cnt ?> लंबित) →</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>रचनाकार</th>
                <th>विधा</th>
                <th>शीर्षक</th>
                <th>दिनांक</th>
                <th>स्थिति</th>
                <th style="text-align: right;">क्रिया</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($recent_submissions && $recent_submissions->num_rows > 0): ?>
                <?php while ($s = $recent_submissions->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($s['author_name']) ?></strong><br>
                            <small style="color: #64748b;"><?= htmlspecialchars($s['author_email']) ?></small>
                        </td>
                        <td><span style="font-size: 12px; text-transform: capitalize; font-weight: bold;"><?= $s['content_type'] ?></span></td>
                        <td><strong><?= htmlspecialchars($s['title']) ?></strong></td>
                        <td><small><?= format_hindi_date($s['created_at']) ?></small></td>
                        <td>
                            <span class="badge" style="background: <?= $s['status'] == 'approved' ? '#dcfce7; color:#166534;' : ($s['status'] == 'pending' ? '#fef3c7; color:#92400e;' : '#fee2e2; color:#991b1b;') ?>">
                                <?= $s['status'] ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="manage_submissions.php?status=pending" class="btn-action btn-edit">समीक्षा करें →</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center; color:#64748b; padding: 24px;">कोई नया रचना प्रस्ताव नहीं आया है।</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Recent Comments & Inquiries -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
    <!-- Comments Table -->
    <div class="admin-table-container">
        <div style="padding: 16px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--admin-border);">
            <span>हालिया टिप्पणियाँ (Recent Comments)</span>
            <a href="manage_comments.php" style="font-size: 13px; color: #2563eb; text-decoration: none;">सभी देखें →</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>नाम</th>
                    <th>विधा</th>
                    <th>टिप्पणी</th>
                    <th>स्थिति</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_comments && $recent_comments->num_rows > 0): ?>
                    <?php while ($c = $recent_comments->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                            <td><span style="font-size: 12px; text-transform: capitalize;"><?= $c['content_type'] ?></span></td>
                            <td><small><?= htmlspecialchars(make_excerpt($c['comment'], 60)) ?></small></td>
                            <td>
                                <span class="badge" style="background: <?= $c['status'] == 'approved' ? '#dcfce7; color:#166534;' : ($c['status'] == 'pending' ? '#fef3c7; color:#92400e;' : '#fee2e2; color:#991b1b;') ?>">
                                    <?= $c['status'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; color:#64748b;">कोई टिप्पणी नहीं मिली।</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Contacts Table -->
    <div class="admin-table-container">
        <div style="padding: 16px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--admin-border);">
            <span>हालिया संदेश (Contact Messages)</span>
            <a href="manage_contacts.php" style="font-size: 13px; color: #2563eb; text-decoration: none;">सभी देखें →</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>प्रेषक</th>
                    <th>विषय</th>
                    <th>दिनांक</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_contacts && $recent_contacts->num_rows > 0): ?>
                    <?php while ($msg = $recent_contacts->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($msg['name']) ?></strong><br>
                                <small style="color:#64748b;"><?= htmlspecialchars($msg['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars(make_excerpt($msg['subject'], 40)) ?></td>
                            <td><small><?= format_hindi_date($msg['created_at']) ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center; color:#64748b;">कोई नया संदेश नहीं मिला।</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>