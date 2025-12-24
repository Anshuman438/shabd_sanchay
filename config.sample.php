<?php
define('DB_SERVER', 'DB_SERVER');
define('DB_USERNAME', 'DB_USERNAME');
define('DB_PASSWORD', 'DB_PASSWORD');
define('DB_NAME', 'DB_NAME');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed");
}

$conn->set_charset("utf8mb4");
?>
