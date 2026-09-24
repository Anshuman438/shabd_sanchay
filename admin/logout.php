<?php
// admin/logout.php
require_once __DIR__ . '/../includes/auth.php';
logout_admin();
header('Location: login.php?msg=logged_out');
exit();
?>
