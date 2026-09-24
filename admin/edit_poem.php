<?php
// admin/edit_poem.php - Redirect to modern manage_poems
$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    header('Location: manage_poems.php?action=edit&id=' . $id);
} else {
    header('Location: manage_poems.php');
}
exit();
?>
