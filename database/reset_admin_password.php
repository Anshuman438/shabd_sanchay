<?php
// database/reset_admin_password.php - Reset Admin Password via CLI ONLY
if (php_sapi_name() !== 'cli') {
    die("Access Denied: This script can only be executed via Command Line (CLI).");
}
require_once __DIR__ . '/../config.php';

$username = $argv[1] ?? 'admin';
$new_pass = $argv[2] ?? 'admin123';

$hash = password_hash($new_pass, PASSWORD_BCRYPT);

$stmt = $conn->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ? OR email = ?");
$stmt->bind_param("sss", $hash, $username, $username);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "SUCCESS: Password for admin user '$username' has been reset to '$new_pass'!\n";
} else {
    // Try inserting if admin user doesn't exist
    $ins = $conn->prepare("INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES (?, 'admin@hindisahitya.com', ?, 'मुख्य प्रशासक', 'superadmin') ON DUPLICATE KEY UPDATE password_hash = ?");
    $ins->bind_param("sss", $username, $hash, $hash);
    if ($ins->execute()) {
        echo "SUCCESS: Admin user '$username' created/updated with password '$new_pass'!\n";
    } else {
        echo "ERROR: Could not reset password: " . $conn->error . "\n";
    }
}
