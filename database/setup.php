<?php
require_once __DIR__ . '/../config.php';

echo "<h2>शब्द संचय (Shabd Sanchay) - Database Setup</h2>";

$sqlFile = __DIR__ . '/schema.sql';
if (!file_exists($sqlFile)) {
    die("<p style='color:red;'>Error: database/schema.sql file not found!</p>");
}

$sqlContent = file_get_contents($sqlFile);
if ($conn->multi_query($sqlContent)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    echo "<p style='color:green; font-weight:bold;'>✓ Database and tables created with sample data successfully!</p>";
    echo "<p><a href='../index.php'>Go to Homepage</a> | <a href='../admin/login.php'>Go to Admin Login</a></p>";
} else {
    echo "<p style='color:red;'>Error setting up database: " . $conn->error . "</p>";
}
?>
