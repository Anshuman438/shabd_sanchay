<?php
// admin/manage_about.php - Admin About Us & Team Management Portal
$page_title = "हमारे बारे में (About Us) प्रबंधन";
require_once __DIR__ . '/header.php';

$tab = $_GET['tab'] ?? 'content';
$msg = '';
$error = '';

// -------------------------------------------------------------
// POST HANDLERS
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. UPDATE ABOUT CONTENT
    if ($action === 'update_content') {
        $fields = [
            'hero_eyebrow' => trim($_POST['hero_eyebrow'] ?? ''),
            'hero_title' => trim($_POST['hero_title'] ?? ''),
            'hero_lead' => trim($_POST['hero_lead'] ?? ''),
            'story_badge' => trim($_POST['story_badge'] ?? ''),
            'story_title' => trim($_POST['story_title'] ?? ''),
            'story_p1' => trim($_POST['story_p1'] ?? ''),
            'story_p2' => trim($_POST['story_p2'] ?? ''),
            'story_p3' => trim($_POST['story_p3'] ?? ''),
            'seal_quote' => trim($_POST['seal_quote'] ?? ''),
            'seal_author' => trim($_POST['seal_author'] ?? ''),
            'seal_tagline' => trim($_POST['seal_tagline'] ?? '')
        ];

        foreach ($fields as $k => $v) {
            $stmt = $conn->prepare("INSERT INTO about_page_content (key_name, content_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE content_value = ?");
            $stmt->bind_param("sss", $k, $v, $v);
            $stmt->execute();
            $stmt->close();
        }
        $msg = "'हमारे बारे में' पृष्ठ की सामग्री सफलतापूर्वक अपडेट कर दी गई!";
        $tab = 'content';
    }

    // 2. TEAM MEMBER ACTIONS
    elseif ($action === 'add_team') {
        $name = trim($_POST['name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $image_url = trim($_POST['image_url'] ?? 'images/authors/author-default.jpg');

        if (!empty($name) && !empty($position)) {
            $stmt = $conn->prepare("INSERT INTO team_members (name, position, bio, image_url) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $position, $bio, $image_url);
            if ($stmt->execute()) {
                $msg = "नया संपादकीय सदस्य सफलतापूर्वक जोड़ा गया!";
            } else {
                $error = "त्रुटि: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "कृपया नाम और पद/दायित्व अवश्य भरें।";
        }
        $tab = 'team';
    } elseif ($action === 'edit_team') {
        $id = intval($_POST['member_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $image_url = trim($_POST['image_url'] ?? '');

        if ($id > 0 && !empty($name) && !empty($position)) {
            $stmt = $conn->prepare("UPDATE team_members SET name = ?, position = ?, bio = ?, image_url = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $position, $bio, $image_url, $id);
            if ($stmt->execute()) {
                $msg = "संपादकीय सदस्य की जानकारी अपडेट कर दी गई!";
            } else {
                $error = "अपडेट त्रुटि: " . $stmt->error;
            }
            $stmt->close();
        }
        $tab = 'team';
    } elseif ($action === 'delete_team') {
        $id = intval($_POST['member_id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $msg = "सदस्य को सफलतापूर्वक हटा दिया गया।";
        }
        $tab = 'team';
    }

    // 3. TESTIMONIAL ACTIONS
    elseif ($action === 'add_testimonial') {
        $name = trim($_POST['name'] ?? '');
        $location = trim($_POST['location'] ?? 'साहित्य-प्रेमी');
        $content = trim($_POST['content'] ?? '');

        if (!empty($name) && !empty($content)) {
            $stmt = $conn->prepare("INSERT INTO testimonials (name, location, content, approved) VALUES (?, ?, ?, 1)");
            $stmt->bind_param("sss", $name, $location, $content);
            if ($stmt->execute()) {
                $msg = "नई पाठक अनुभूति सफलतापूर्वक जोड़ी गई!";
            } else {
                $error = "त्रुटि: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "कृपया नाम और समीक्षा सामग्री भरें।";
        }
        $tab = 'testimonials';
    } elseif ($action === 'delete_testimonial') {
        $id = intval($_POST['test_id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $msg = "पाठक समीक्षा को हटा दिया गया।";
        }
        $tab = 'testimonials';
    }
}

// -------------------------------------------------------------
// FETCH CURRENT DATA
// -------------------------------------------------------------
// Fetch about content
$about_data = [];
$res = $conn->query("SELECT key_name, content_value FROM about_page_content");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $about_data[$r['key_name']] = $r['content_value'];
    }
}

// Fetch team members
$team_members = $conn->query("SELECT * FROM team_members ORDER BY id ASC");

// Fetch testimonials
$testimonials = $conn->query("SELECT * FROM testimonials ORDER BY id DESC");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 24px; margin-bottom: 6px;">हमारे बारे में (About Us) प्रबंधन</h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">मंच की दृष्टि, साहित्यिक यात्रा, संपादकीय मंडल एवं पाठक अनुभूतियों का नियंत्रण।</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="../about.php" target="_blank" class="btn-action btn-edit" style="padding: 8px 16px;">
            <span>↗ लाइव 'हमारे बारे में' पृष्ठ देखें</span>
        </a>
    </div>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Tabs Navigation -->
<div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--admin-border); padding-bottom: 12px;">
    <a href="manage_about.php?tab=content" class="btn-action <?= $tab === 'content' ? 'btn-approve' : 'btn-secondary' ?>" style="padding: 9px 18px; border-radius: 8px; font-weight: 600;">
        📝 मुख्य परिचय एवं दृष्टि (Narrative)
    </a>
    <a href="manage_about.php?tab=team" class="btn-action <?= $tab === 'team' ? 'btn-approve' : 'btn-secondary' ?>" style="padding: 9px 18px; border-radius: 8px; font-weight: 600;">
        👥 संपादकीय मंडल एवं टीम (Team)
    </a>
    <a href="manage_about.php?tab=testimonials" class="btn-action <?= $tab === 'testimonials' ? 'btn-approve' : 'btn-secondary' ?>" style="padding: 9px 18px; border-radius: 8px; font-weight: 600;">
        💬 पाठक अनुभूतियाँ (Testimonials)
    </a>
</div>

<?php if ($tab === 'content'): ?>
    <!-- TAB 1: ABOUT CONTENT FORM -->
    <div class="form-card">
        <h2 style="font-size: 18px; margin-bottom: 20px; border-bottom: 1px solid var(--admin-border); padding-bottom: 10px;">मुख्य परिचय एवं साहित्यिक दर्शन</h2>
        
        <form action="manage_about.php?tab=content" method="POST">
            <input type="hidden" name="action" value="update_content">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>आईब्रो टैगलाइन (Eyebrow)</label>
                    <input type="text" name="hero_eyebrow" class="form-control" value="<?= htmlspecialchars($about_data['hero_eyebrow'] ?? 'हमारी दृष्टि, यात्रा एवं साहित्य-साधना • ABOUT SHABD SANCHAY') ?>">
                </div>
                <div class="form-group">
                    <label>मुख्य पृष्ठ शीर्षक (Main Heading)</label>
                    <input type="text" name="hero_title" class="form-control" value="<?= htmlspecialchars($about_data['hero_title'] ?? 'शब्द संचय : विचारों के नए प्रतिमान') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>परिचय उपशीर्षक (Lead Subtitle)</label>
                <textarea name="hero_lead" class="form-control" rows="2"><?= htmlspecialchars($about_data['hero_lead'] ?? '') ?></textarea>
            </div>

            <hr style="margin: 24px 0; border: 0; border-top: 1px solid var(--admin-border);">

            <h3 style="font-size: 16px; margin-bottom: 16px;">हमारी साहित्यिक यात्रा (Our Genesis Section)</h3>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
                <div class="form-group">
                    <label>यात्रा बैज (Story Badge)</label>
                    <input type="text" name="story_badge" class="form-control" value="<?= htmlspecialchars($about_data['story_badge'] ?? 'हमारी यात्रा • OUR GENESIS') ?>">
                </div>
                <div class="form-group">
                    <label>यात्रा शीर्षक (Story Title)</label>
                    <input type="text" name="story_title" class="form-control" value="<?= htmlspecialchars($about_data['story_title'] ?? 'शब्दों का संचय, संवेदनाओं का विस्तार') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>अनुच्छेद 1 (Paragraph 1 - नींव और आवश्यकता)</label>
                <textarea name="story_p1" class="form-control" rows="3"><?= htmlspecialchars($about_data['story_p1'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>अनुच्छेद 2 (Paragraph 2 - परंपरा व आधुनिकता)</label>
                <textarea name="story_p2" class="form-control" rows="3"><?= htmlspecialchars($about_data['story_p2'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>अनुच्छेद 3 (Paragraph 3 - आज का विस्तार)</label>
                <textarea name="story_p3" class="form-control" rows="3"><?= htmlspecialchars($about_data['story_p3'] ?? '') ?></textarea>
            </div>

            <hr style="margin: 24px 0; border: 0; border-top: 1px solid var(--admin-border);">

            <h3 style="font-size: 16px; margin-bottom: 16px;">साहित्यिक मुहर / दर्शन उद्धरण (Philosophical Quote Box)</h3>

            <div class="form-group">
                <label>उद्धरण (Quote)</label>
                <input type="text" name="seal_quote" class="form-control" value="<?= htmlspecialchars($about_data['seal_quote'] ?? '“शब्द केवल अक्षर नहीं होते, वे मनुष्य की चेतना, विचार और आत्मीय अनुभूतियों का जीवंत आलोक हैं।”') ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>उद्धरण कर्ता (Quote Author)</label>
                    <input type="text" name="seal_author" class="form-control" value="<?= htmlspecialchars($about_data['seal_author'] ?? '— शब्द संचय साहित्य दर्शन') ?>">
                </div>
                <div class="form-group">
                    <label>टैगलाइन (Tagline)</label>
                    <input type="text" name="seal_tagline" class="form-control" value="<?= htmlspecialchars($about_data['seal_tagline'] ?? 'साहित्य • संस्कृति • चिंतन') ?>">
                </div>
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="btn-action btn-approve" style="padding: 10px 24px; font-weight: 700; font-size: 15px;">💾 परिवर्तन सहेजें (Save Changes)</button>
            </div>
        </form>
    </div>

<?php elseif ($tab === 'team'): ?>
    <!-- TAB 2: TEAM MEMBERS MANAGEMENT -->
    <div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px; align-items: start;">
        <!-- Add Team Member Form -->
        <div class="form-card">
            <h3 style="font-size: 17px; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">+ नया संपादकीय सदस्य जोड़ें</h3>
            <form action="manage_about.php?tab=team" method="POST">
                <input type="hidden" name="action" value="add_team">

                <div class="form-group">
                    <label>सदस्य का नाम</label>
                    <input type="text" name="name" class="form-control" placeholder="उदा. अंशुमन सिंह" required>
                </div>

                <div class="form-group">
                    <label>पद / संपादकीय भूमिका (Position / Role)</label>
                    <input type="text" name="position" class="form-control" placeholder="उदा. संस्थापक एवं मुख्य संपादक" required>
                </div>

                <div class="form-group">
                    <label>संक्षिप्त परिचय (Bio)</label>
                    <textarea name="bio" class="form-control" rows="3" placeholder="साहित्यिक पृष्ठभूमि या योगदान..."></textarea>
                </div>

                <div class="form-group">
                    <label>फ़ोटो पाथ (Photo URL / Path)</label>
                    <input type="text" name="image_url" class="form-control" value="images/authors/author-default.jpg">
                </div>

                <button type="submit" class="btn-action btn-approve" style="padding: 9px 20px; font-weight: 700;">+ सदस्य जोड़ें</button>
            </form>
        </div>

        <!-- Current Team Members List -->
        <div>
            <h3 style="font-size: 17px; margin-bottom: 14px;">वर्तमान संपादकीय मंडल (<?= $team_members ? $team_members->num_rows : 0 ?> सदस्य)</h3>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>फ़ोटो</th>
                            <th>नाम व दायित्व</th>
                            <th>संक्षिप्त परिचय</th>
                            <th style="text-align: right;">क्रिया</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($team_members && $team_members->num_rows > 0): ?>
                            <?php while ($m = $team_members->fetch_assoc()): ?>
                                <tr>
                                    <td style="width: 50px;">
                                        <img src="../<?= htmlspecialchars($m['image_url'] ?: 'images/authors/author-default.jpg') ?>" alt="Member" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1;">
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($m['name']) ?></strong><br>
                                        <small style="color: #2563eb; font-weight: 600;"><?= htmlspecialchars($m['position']) ?></small>
                                    </td>
                                    <td style="max-width: 260px; font-size: 13px; color: var(--admin-text-muted);">
                                        <?= htmlspecialchars(mb_substr($m['bio'] ?? '', 0, 75)) ?>...
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <form action="manage_about.php?tab=team" method="POST" style="display: inline-block;" onsubmit="return confirm('क्या आप इस सदस्य को हटाना चाहते हैं?');">
                                            <input type="hidden" name="action" value="delete_team">
                                            <input type="hidden" name="member_id" value="<?= $m['id'] ?>">
                                            <button type="submit" class="btn-action btn-delete">✕ हटाएँ</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 25px; color: #64748b;">कोई सदस्य उपलब्ध नहीं है।</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php elseif ($tab === 'testimonials'): ?>
    <!-- TAB 3: TESTIMONIALS MANAGEMENT -->
    <div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px; align-items: start;">
        <!-- Add Testimonial Form -->
        <div class="form-card">
            <h3 style="font-size: 17px; margin-bottom: 16px; border-bottom: 1px solid var(--admin-border); padding-bottom: 8px;">+ नई पाठक अनुभूति जोड़ें</h3>
            <form action="manage_about.php?tab=testimonials" method="POST">
                <input type="hidden" name="action" value="add_testimonial">

                <div class="form-group">
                    <label>पाठक / समीक्षक का नाम</label>
                    <input type="text" name="name" class="form-control" placeholder="उदा. डॉ. अवधेश कुमार" required>
                </div>

                <div class="form-group">
                    <label>स्थान / पहचान (Location / Designation)</label>
                    <input type="text" name="location" class="form-control" placeholder="उदा. वाराणसी • प्राध्यापक एवं समीक्षक">
                </div>

                <div class="form-group">
                    <label>अनुभूति / समीक्षा (Review Content)</label>
                    <textarea name="content" class="form-control" rows="4" placeholder="पाठक द्वारा कही गई बात..." required></textarea>
                </div>

                <button type="submit" class="btn-action btn-approve" style="padding: 9px 20px; font-weight: 700;">+ अनुभूति जोड़ें</button>
            </form>
        </div>

        <!-- Current Testimonials List -->
        <div>
            <h3 style="font-size: 17px; margin-bottom: 14px;">पाठक अनुभूतियाँ (<?= $testimonials ? $testimonials->num_rows : 0 ?>)</h3>
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>पाठक का नाम व स्थान</th>
                            <th>अनुभूति (Quote)</th>
                            <th style="text-align: right;">क्रिया</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($testimonials && $testimonials->num_rows > 0): ?>
                            <?php while ($t = $testimonials->fetch_assoc()): ?>
                                <tr>
                                    <td style="white-space: nowrap;">
                                        <strong><?= htmlspecialchars($t['name']) ?></strong><br>
                                        <small style="color: var(--admin-text-muted);"><?= htmlspecialchars($t['location']) ?></small>
                                    </td>
                                    <td style="font-size: 13px; font-style: italic; color: var(--admin-text);">
                                        “<?= htmlspecialchars(mb_substr($t['content'], 0, 100)) ?>...”
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <form action="manage_about.php?tab=testimonials" method="POST" style="display: inline-block;" onsubmit="return confirm('क्या आप इस समीक्षा को हटाना चाहते हैं?');">
                                            <input type="hidden" name="action" value="delete_testimonial">
                                            <input type="hidden" name="test_id" value="<?= $t['id'] ?>">
                                            <button type="submit" class="btn-action btn-delete">✕ हटाएँ</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 25px; color: #64748b;">कोई समीक्षा उपलब्ध नहीं है।</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
