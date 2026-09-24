<?php
// admin/manage_submissions.php - Admin User Submissions Approval Portal
$page_title = "रचना प्रस्ताव एवं अनुमोदन (User Submissions)";
require_once __DIR__ . '/header.php';

$action = $_GET['action'] ?? 'list';
$sub_id = intval($_GET['id'] ?? 0);
$filter_status = $_GET['status'] ?? 'pending';
$filter_type = $_GET['type'] ?? 'all';

$alert_msg = '';
$alert_type = '';

// -------------------------------------------------------------
// POST ACTIONS: Approve & Publish, Edit & Publish, Reject, Delete
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['post_action'] ?? '';
    $id = intval($_POST['submission_id'] ?? 0);

    if ($post_action === 'approve' && $id > 0) {
        // Fetch submission
        $stmt = $conn->prepare("SELECT * FROM user_submissions WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $sub = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($sub) {
            $type = $sub['content_type'];
            $title = $sub['title'];
            $author = $sub['author_name'];
            $category = $sub['category'] ?: 'सामान्य';
            $content = normalize_content_text($sub['content']);
            $excerpt = $sub['excerpt'] ? normalize_content_text($sub['excerpt']) : make_excerpt($content, 140);
            $image_url = $sub['image_url'] ?: 'images/featured-1.jpg';
            $published_id = 0;

            if ($type === 'poem') {
                $ins = $conn->prepare("INSERT INTO poems (title, content, author_name, category, image_url) VALUES (?, ?, ?, ?, ?)");
                $ins->bind_param("sssss", $title, $content, $author, $category, $image_url);
                if ($ins->execute()) {
                    $published_id = $ins->insert_id;
                }
                $ins->close();
            } elseif ($type === 'article') {
                $read_time = estimate_reading_time($content);
                $ins = $conn->prepare("INSERT INTO articles (title, excerpt, content, author_name, category, read_time, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ins->bind_param("sssssis", $title, $excerpt, $content, $author, $category, $read_time, $image_url);
                if ($ins->execute()) {
                    $published_id = $ins->insert_id;
                }
                $ins->close();
            } elseif ($type === 'story') {
                $read_time = estimate_reading_time($content);
                $ins = $conn->prepare("INSERT INTO stories (title, excerpt, content, author_name, category, read_time, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ins->bind_param("sssssis", $title, $excerpt, $content, $author, $category, $read_time, $image_url);
                if ($ins->execute()) {
                    $published_id = $ins->insert_id;
                }
                $ins->close();
            } elseif ($type === 'play') {
                $acts_count = 3;
                $ins = $conn->prepare("INSERT INTO plays (title, excerpt, content, author_name, category, acts_count, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ins->bind_param("sssssis", $title, $excerpt, $content, $author, $category, $acts_count, $image_url);
                if ($ins->execute()) {
                    $published_id = $ins->insert_id;
                }
                $ins->close();
            }

            if ($published_id > 0) {
                // Update submission status
                $now = date('Y-m-d H:i:s');
                $upd = $conn->prepare("UPDATE user_submissions SET status = 'approved', published_content_id = ?, reviewed_at = ? WHERE id = ?");
                $upd->bind_param("isi", $published_id, $now, $id);
                $upd->execute();
                $upd->close();

                $alert_msg = "रचना '{$title}' को सफलतापूर्वक स्वीकृत कर मुख्य वेबसाइट पर प्रकाशित कर दिया गया है!";
                $alert_type = "success";
            } else {
                $alert_msg = "रचना प्रकाशित करने में त्रुटि आई: " . $conn->error;
                $alert_type = "danger";
            }
        }
    } elseif ($post_action === 'edit_and_approve' && $id > 0) {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author_name'] ?? '');
        $category = trim($_POST['category'] ?? 'सामान्य');
        $excerpt = normalize_content_text($_POST['excerpt'] ?? '');
        $content = normalize_content_text($_POST['content'] ?? '');
        $type = trim($_POST['content_type'] ?? 'poem');
        $image_url = trim($_POST['image_url'] ?? 'images/featured-1.jpg');
        $published_id = 0;

        if ($type === 'poem') {
            $ins = $conn->prepare("INSERT INTO poems (title, content, author_name, category, image_url) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("sssss", $title, $content, $author, $category, $image_url);
            if ($ins->execute()) $published_id = $ins->insert_id;
            $ins->close();
        } elseif ($type === 'article') {
            $read_time = estimate_reading_time($content);
            $ins = $conn->prepare("INSERT INTO articles (title, excerpt, content, author_name, category, read_time, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->bind_param("sssssis", $title, $excerpt, $content, $author, $category, $read_time, $image_url);
            if ($ins->execute()) $published_id = $ins->insert_id;
            $ins->close();
        } elseif ($type === 'story') {
            $read_time = estimate_reading_time($content);
            $ins = $conn->prepare("INSERT INTO stories (title, excerpt, content, author_name, category, read_time, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->bind_param("sssssis", $title, $excerpt, $content, $author, $category, $read_time, $image_url);
            if ($ins->execute()) $published_id = $ins->insert_id;
            $ins->close();
        } elseif ($type === 'play') {
            $acts_count = 3;
            $ins = $conn->prepare("INSERT INTO plays (title, excerpt, content, author_name, category, acts_count, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->bind_param("sssssis", $title, $excerpt, $content, $author, $category, $acts_count, $image_url);
            if ($ins->execute()) $published_id = $ins->insert_id;
            $ins->close();
        }

        if ($published_id > 0) {
            $now = date('Y-m-d H:i:s');
            $upd = $conn->prepare("UPDATE user_submissions SET title = ?, author_name = ?, category = ?, excerpt = ?, content = ?, status = 'approved', published_content_id = ?, reviewed_at = ? WHERE id = ?");
            $upd->bind_param("sssssisi", $title, $author, $category, $excerpt, $content, $published_id, $now, $id);
            $upd->execute();
            $upd->close();

            $alert_msg = "रचना को संपादित कर सफलतापूर्वक प्रकाशित कर दिया गया है!";
            $alert_type = "success";
        }
    } elseif ($post_action === 'reject' && $id > 0) {
        $note = trim($_POST['admin_note'] ?? '');
        $now = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE user_submissions SET status = 'rejected', admin_note = ?, reviewed_at = ? WHERE id = ?");
        $stmt->bind_param("ssi", $note, $now, $id);
        $stmt->execute();
        $stmt->close();
        $alert_msg = "रचना प्रस्ताव को अस्वीकृत कर दिया गया है।";
        $alert_type = "danger";
    } elseif ($post_action === 'delete' && $id > 0) {
        $stmt = $conn->prepare("DELETE FROM user_submissions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $alert_msg = "प्रस्ताव सफलतापूर्वक हटा दिया गया।";
        $alert_type = "success";
    }
}

// -------------------------------------------------------------
// GET COUNTS
// -------------------------------------------------------------
$count_pending = $conn->query("SELECT COUNT(*) as c FROM user_submissions WHERE status = 'pending'")->fetch_assoc()['c'] ?? 0;
$count_approved = $conn->query("SELECT COUNT(*) as c FROM user_submissions WHERE status = 'approved'")->fetch_assoc()['c'] ?? 0;
$count_rejected = $conn->query("SELECT COUNT(*) as c FROM user_submissions WHERE status = 'rejected'")->fetch_assoc()['c'] ?? 0;
$count_all = $conn->query("SELECT COUNT(*) as c FROM user_submissions")->fetch_assoc()['c'] ?? 0;

// -------------------------------------------------------------
// FETCH SUBMISSIONS
// -------------------------------------------------------------
$where_clauses = [];
$params = [];
$types = "";

if ($filter_status !== 'all') {
    $where_clauses[] = "status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

if ($filter_type !== 'all') {
    $where_clauses[] = "content_type = ?";
    $params[] = $filter_type;
    $types .= "s";
}

$sql = "SELECT * FROM user_submissions";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$submissions = $stmt->get_result();
$stmt->close();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 24px; margin-bottom: 6px;">रचना प्रस्ताव एवं अनुमोदन (User Submissions)</h1>
        <p style="color: #64748b; font-size: 14px; margin: 0;">पाठकों एवं रचनाकारों द्वारा भेजी गई मौलिक रचनाओं की समीक्षा व त्वरित प्रकाशन।</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="../submit.php" target="_blank" class="btn-action btn-edit" style="padding: 8px 16px;">
            <span>+ सार्वजनिक सबमिशन फ़ॉर्म देखें</span>
        </a>
    </div>
</div>

<?php if (!empty($alert_msg)): ?>
    <div class="alert alert-<?= $alert_type ?>"><?= htmlspecialchars($alert_msg) ?></div>
<?php endif; ?>

<!-- Status Filter Tabs -->
<div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
    <a href="manage_submissions.php?status=pending&type=<?= $filter_type ?>" class="btn-action <?= $filter_status === 'pending' ? 'btn-approve' : '' ?>" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--admin-border); <?= $filter_status === 'pending' ? 'font-weight: 700;' : 'background: var(--admin-card-bg); color: var(--admin-text);' ?>">
        ⏳ लंबित (Pending) (<?= $count_pending ?>)
    </a>
    <a href="manage_submissions.php?status=approved&type=<?= $filter_type ?>" class="btn-action <?= $filter_status === 'approved' ? 'btn-approve' : '' ?>" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--admin-border); <?= $filter_status === 'approved' ? 'font-weight: 700;' : 'background: var(--admin-card-bg); color: var(--admin-text);' ?>">
        ✓ स्वीकृत व प्रकाशित (<?= $count_approved ?>)
    </a>
    <a href="manage_submissions.php?status=rejected&type=<?= $filter_type ?>" class="btn-action <?= $filter_status === 'rejected' ? 'btn-delete' : '' ?>" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--admin-border); <?= $filter_status === 'rejected' ? 'font-weight: 700;' : 'background: var(--admin-card-bg); color: var(--admin-text);' ?>">
        ✕ अस्वीकृत (<?= $count_rejected ?>)
    </a>
    <a href="manage_submissions.php?status=all&type=<?= $filter_type ?>" class="btn-action" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--admin-border); <?= $filter_status === 'all' ? 'background: #2563eb; color: white;' : 'background: var(--admin-card-bg); color: var(--admin-text);' ?>">
        📋 सभी (<?= $count_all ?>)
    </a>
</div>

<!-- Table Card -->
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 60px;">ID</th>
                <th>रचनाकार (Author)</th>
                <th>विधा</th>
                <th>शीर्षक</th>
                <th>दिनांक</th>
                <th>स्थिति (Status)</th>
                <th style="text-align: right;">क्रियाएँ (Actions)</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($submissions && $submissions->num_rows > 0): ?>
                <?php while ($sub = $submissions->fetch_assoc()): 
                    $type_labels = [
                        'poem' => ['label' => 'कविता (Poem)', 'color' => '#0284c7', 'bg' => '#e0f2fe'],
                        'article' => ['label' => 'लेख (Article)', 'color' => '#d97706', 'bg' => '#fef3c7'],
                        'story' => ['label' => 'कहानी (Story)', 'color' => '#16a34a', 'bg' => '#dcfce7'],
                        'play' => ['label' => 'नाटक (Play)', 'color' => '#9333ea', 'bg' => '#f3e8ff']
                    ];
                    $t_info = $type_labels[$sub['content_type']] ?? ['label' => $sub['content_type'], 'color' => '#64748b', 'bg' => '#f1f5f9'];
                ?>
                    <tr>
                        <td><strong>#<?= $sub['id'] ?></strong></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="../<?= htmlspecialchars($sub['author_photo'] ?: 'images/authors/author-default.jpg') ?>" alt="Author" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid #cbd5e1;">
                                <div>
                                    <strong><?= htmlspecialchars($sub['author_name']) ?></strong><br>
                                    <small style="color: #64748b;"><?= htmlspecialchars($sub['author_email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: <?= $t_info['bg'] ?>; color: <?= $t_info['color'] ?>; font-weight: 700;">
                                <?= $t_info['label'] ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($sub['title']) ?></strong>
                            <div style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($sub['category']) ?></div>
                        </td>
                        <td><small><?= format_hindi_date($sub['created_at']) ?></small></td>
                        <td>
                            <?php if ($sub['status'] === 'approved'): ?>
                                <span class="badge" style="background: #dcfce7; color: #166534; font-weight: bold;">स्वीकृत ✓</span>
                                <?php if ($sub['published_content_id']): 
                                    $link_map = [
                                        'poem' => "../poem.php?id={$sub['published_content_id']}",
                                        'article' => "../article.php?id={$sub['published_content_id']}",
                                        'story' => "../story.php?id={$sub['published_content_id']}",
                                        'play' => "../play_detail.php?id={$sub['published_content_id']}"
                                    ];
                                    $view_url = $link_map[$sub['content_type']] ?? '#';
                                ?>
                                    <br><a href="<?= $view_url ?>" target="_blank" style="font-size: 11px; color: #2563eb; font-weight: 600;">लाइव देखें ↗</a>
                                <?php endif; ?>
                            <?php elseif ($sub['status'] === 'rejected'): ?>
                                <span class="badge" style="background: #fee2e2; color: #991b1b; font-weight: bold;">अस्वीकृत ✕</span>
                            <?php else: ?>
                                <span class="badge" style="background: #fef3c7; color: #92400e; font-weight: bold;">लंबित (Pending)</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <!-- View Details Button -->
                            <button type="button" class="btn-action btn-edit" onclick="openViewModal(<?= htmlspecialchars(json_encode($sub), ENT_QUOTES, 'UTF-8') ?>)">
                                देखें व समीक्षा
                            </button>

                            <?php if ($sub['status'] === 'pending'): ?>
                                <!-- Quick Approve Form -->
                                <form action="manage_submissions.php?status=<?= $filter_status ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('क्या आप इस रचना को स्वीकृत कर तुरंत मुख्य वेबसाइट पर प्रकाशित करना चाहते हैं?');">
                                    <input type="hidden" name="post_action" value="approve">
                                    <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                                    <button type="submit" class="btn-action btn-approve" title="स्वीकृत करें व प्रकाशित करें">✓ प्रकाशित करें</button>
                                </form>
                            <?php endif; ?>

                            <!-- Delete Form -->
                            <form action="manage_submissions.php?status=<?= $filter_status ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('क्या आप इस प्रस्ताव को हटाना चाहते हैं?');">
                                <input type="hidden" name="post_action" value="delete">
                                <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                                <button type="submit" class="btn-action btn-delete" title="हटाएँ">✕</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">
                        कोई रचना प्रस्ताव नहीं मिला।
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Detailed Review & Edit Modal -->
<div id="review-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 9999; overflow-y: auto; padding: 30px 15px;">
    <div style="background: var(--admin-card-bg); max-width: 780px; margin: 0 auto; border-radius: 16px; padding: 28px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); position: relative;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--admin-border); padding-bottom: 14px; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 20px;">रचना समीक्षा एवं संपादन</h3>
            <button type="button" onclick="closeViewModal()" style="border: none; background: transparent; font-size: 24px; cursor: pointer; color: var(--admin-text);">&times;</button>
        </div>

        <form action="manage_submissions.php?status=<?= $filter_status ?>" method="POST" id="modal-review-form">
            <input type="hidden" name="post_action" id="modal-post-action" value="edit_and_approve">
            <input type="hidden" name="submission_id" id="modal-sub-id" value="0">
            <input type="hidden" name="content_type" id="modal-content-type" value="poem">
            <input type="hidden" name="image_url" id="modal-image-url" value="">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                <div class="form-group">
                    <label>रचनाकार का नाम</label>
                    <input type="text" name="author_name" id="modal-author" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>ईमेल</label>
                    <input type="text" id="modal-email" class="form-control" readonly style="opacity: 0.75; cursor: not-allowed;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 14px; margin-bottom: 14px;">
                <div class="form-group">
                    <label>रचना का शीर्षक</label>
                    <input type="text" name="title" id="modal-title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>विधा / श्रेणी</label>
                    <input type="text" name="category" id="modal-category" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>संक्षिप्त सारांश (Excerpt)</label>
                <input type="text" name="excerpt" id="modal-excerpt" class="form-control">
            </div>

            <div class="form-group">
                <label>मुख्य रचना / पाठ (Main Content)</label>
                <textarea name="content" id="modal-content" class="form-control" rows="10" style="font-family: 'Noto Serif Devanagari', serif; font-size: 15px; line-height: 1.8;" required></textarea>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; border-top: 1px solid var(--admin-border); padding-top: 18px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <button type="button" class="btn-action btn-delete" onclick="rejectCurrentSubmission()">✕ अस्वीकार करें (Reject)</button>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn-action btn-edit" onclick="closeViewModal()">रद्द करें</button>
                    <button type="submit" class="btn-action btn-approve" style="padding: 10px 20px; font-weight: 700;">✓ स्वीकृत कर प्रकाशित करें</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openViewModal(data) {
    document.getElementById('modal-sub-id').value = data.id;
    document.getElementById('modal-content-type').value = data.content_type;
    document.getElementById('modal-author').value = data.author_name;
    document.getElementById('modal-email').value = data.author_email;
    document.getElementById('modal-title').value = data.title;
    document.getElementById('modal-category').value = data.category || 'सामान्य';
    document.getElementById('modal-excerpt').value = data.excerpt || '';
    document.getElementById('modal-content').value = data.content;
    document.getElementById('modal-image-url').value = data.image_url || '';

    document.getElementById('review-modal').style.display = 'block';
}

function closeViewModal() {
    document.getElementById('review-modal').style.display = 'none';
}

function rejectCurrentSubmission() {
    if (confirm('क्या आप निश्चित हैं कि इस रचना प्रस्ताव को अस्वीकार करना चाहते हैं?')) {
        const form = document.getElementById('modal-review-form');
        document.getElementById('modal-post-action').value = 'reject';
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
