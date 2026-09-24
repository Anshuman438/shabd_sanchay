<?php
// admin/manage_poems.php
$page_title = "कविता प्रबंधन";
require_once __DIR__ . '/header.php';

$action = $_GET['action'] ?? 'list';
$id = intval($_GET['id'] ?? 0);
$msg = '';
$error = '';

// Handle Delete
if ($action === 'delete' && $id > 0) {
    if (!validate_csrf_token($_GET['csrf'] ?? '')) {
        $error = "सुरक्षा टोकन अमान्य है।";
    } else {
        $stmt = $conn->prepare("DELETE FROM poems WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $msg = "कविता सफलतापूर्वक हटा दी गई।";
        } else {
            $error = "हटाने में त्रुटि: " . $stmt->error;
        }
        $stmt->close();
    }
    $action = 'list';
}

// Handle Add / Edit POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token()) {
        $error = "सुरक्षा टोकन अमान्य है।";
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $author_name = trim($_POST['author_name'] ?? '');
        $category = trim($_POST['category'] ?? 'सामान्य');
        $image_url = trim($_POST['image_url'] ?? 'images/poetry-default.jpg');
        $edit_id = intval($_POST['edit_id'] ?? 0);

        if (empty($title) || empty($content) || empty($author_name)) {
            $error = "कृपया शीर्षक, रचना और कवि का नाम अवश्य भरें।";
        } else {
            if ($edit_id > 0) {
                $stmt = $conn->prepare("UPDATE poems SET title = ?, content = ?, author_name = ?, category = ?, image_url = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $title, $content, $author_name, $category, $image_url, $edit_id);
                if ($stmt->execute()) {
                    $msg = "कविता सफलतापूर्वक अपडेट की गई!";
                    $action = 'list';
                } else {
                    $error = "अपडेट त्रुटि: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $stmt = $conn->prepare("INSERT INTO poems (title, content, author_name, category, image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $title, $content, $author_name, $category, $image_url);
                if ($stmt->execute()) {
                    $msg = "नई कविता सफलतापूर्वक जोड़ी गई!";
                    $action = 'list';
                } else {
                    $error = "जोड़ने में त्रुटि: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Fetch edit data if needed
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM poems WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$csrf_token = generate_csrf_token();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 22px; margin-bottom: 4px;">कविता प्रबंधन (Manage Poems)</h1>
        <p style="color: #64748b; font-size: 13px;">कविताओं को जोड़ें, संपादित करें या हटाएँ।</p>
    </div>
    <?php if ($action === 'list'): ?>
        <a href="manage_poems.php?action=create" class="btn-action btn-approve" style="padding: 10px 18px; font-size: 14px;">+ नई कविता जोड़ें</a>
    <?php else: ?>
        <a href="manage_poems.php" class="btn-action btn-secondary" style="padding: 10px 18px;">← सूची पर वापस जाएँ</a>
    <?php endif; ?>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($action === 'create' || $action === 'edit'): ?>
    <!-- Create / Edit Form -->
    <div class="form-card">
        <h2 style="font-size: 18px; margin-bottom: 20px;"><?= $action === 'edit' ? 'कविता संपादित करें' : 'नई कविता जोड़ें' ?></h2>
        <form method="POST" action="manage_poems.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?? 0 ?>">

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>कविता का शीर्षक (Title) *</label>
                    <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" placeholder="उदा. प्रकृति की पुकार">
                </div>
                <div class="form-group">
                    <label>कवि / रचयिता (Author) *</label>
                    <input type="text" name="author_name" class="form-control" required value="<?= htmlspecialchars($edit_data['author_name'] ?? '') ?>" placeholder="उदा. हरिवंश राय बच्चन">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>श्रेणी (Category)</label>
                    <select name="category" class="form-control">
                        <?php
                        $cats = ['प्रकृति', 'प्रेम', 'देशभक्ति', 'दार्शनिक', 'प्रेरणादायक', 'सामान्य', 'सामाजिक'];
                        $cur_cat = $edit_data['category'] ?? 'सामान्य';
                        foreach ($cats as $c): ?>
                            <option value="<?= $c ?>" <?= $cur_cat === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>कवर छवि यूआरएल (Image URL)</label>
                    <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($edit_data['image_url'] ?? 'images/poetry-default.jpg') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>कविता की पंक्तियाँ (Poem Content) *</label>
                <textarea name="content" class="form-control" rows="12" required placeholder="यहाँ कविता लिखें..."><?= htmlspecialchars($edit_data['content'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-action btn-approve" style="padding: 12px 24px; font-size: 15px;">
                <?= $action === 'edit' ? 'अपडेट सहेजें' : 'प्रकाशित करें' ?>
            </button>
        </form>
    </div>
<?php else: ?>
    <!-- List Table -->
    <?php
    $poems = $conn->query("SELECT * FROM poems ORDER BY created_at DESC");
    ?>
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>शीर्षक</th>
                    <th>कवि</th>
                    <th>श्रेणी</th>
                    <th>पसंद / दर्शन</th>
                    <th>दिनांक</th>
                    <th style="text-align: right;">क्रियाएँ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($poems && $poems->num_rows > 0): ?>
                    <?php while ($row = $poems->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($row['title']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($row['author_name']) ?></td>
                            <td><span class="badge" style="background:#e0f2fe; color:#0369a1;"><?= htmlspecialchars($row['category']) ?></span></td>
                            <td><?= $row['likes'] ?> पसंद &nbsp;|&nbsp; <?= $row['views'] ?> दर्शन</td>
                            <td><?= format_hindi_date($row['created_at']) ?></td>
                            <td style="text-align: right;">
                                <a href="../poem.php?id=<?= $row['id'] ?>" target="_blank" class="btn-action" style="background:#f1f5f9; color:#475569;" title="देखें">देखें</a>
                                <a href="manage_poems.php?action=edit&id=<?= $row['id'] ?>" class="btn-action btn-edit">संपादित करें</a>
                                <a href="manage_poems.php?action=delete&id=<?= $row['id'] ?>&csrf=<?= $csrf_token ?>" class="btn-action btn-delete" onclick="return confirm('क्या आप वाकई इस कविता को हटाना चाहते हैं?')">हटाएँ</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center; padding: 24px;">कोई कविता उपलब्ध नहीं है।</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
