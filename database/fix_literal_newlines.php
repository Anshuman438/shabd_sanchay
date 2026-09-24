<?php
// database/fix_literal_newlines.php
require_once __DIR__ . '/../config.php';

$tables_columns = [
    'poems' => ['content', 'title'],
    'articles' => ['content', 'excerpt', 'title'],
    'stories' => ['content', 'excerpt', 'title'],
    'plays' => ['content', 'excerpt', 'title'],
    'user_submissions' => ['content', 'excerpt', 'title', 'admin_note']
];

$total_fixed = 0;

foreach ($tables_columns as $table => $columns) {
    $res = $conn->query("SELECT id, " . implode(', ', $columns) . " FROM `$table`");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $updates = [];
            $params = [];
            $types = "";

            foreach ($columns as $col) {
                $val = $row[$col];
                if ($val !== null && (strpos($val, '\n') !== false || strpos($val, '\r') !== false)) {
                    $cleaned = str_replace(["\\r\\n", "\\n", "\\r"], "\n", $val);
                    $updates[] = "`$col` = ?";
                    $params[] = $cleaned;
                    $types .= "s";
                }
            }

            if (!empty($updates)) {
                $sql = "UPDATE `$table` SET " . implode(', ', $updates) . " WHERE id = ?";
                $params[] = $id;
                $types .= "i";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $stmt->close();
                $total_fixed++;
            }
        }
    }
}

echo "Database fixed! Total rows updated: " . $total_fixed . PHP_EOL;
