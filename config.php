<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}
// Disable default exception throwing to handle errors gracefully
mysqli_report(MYSQLI_REPORT_OFF);

// Database configuration
$servername = getenv('DB_HOST') ?: "localhost";
$username   = getenv('DB_USER') ?: "root";
$password   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname     = getenv('DB_NAME') ?: "SHABD_SANCHAY";
$port       = getenv('DB_PORT') ? intval(getenv('DB_PORT')) : 3306;

// Create connection with fallback support
$ports_to_try = getenv('DB_PORT') ? [intval(getenv('DB_PORT'))] : [3306, 3307];
$conn = null;

foreach ($ports_to_try as $p) {
    $test_conn = @new mysqli($servername, $username, $password, $dbname, $p);
    if (!$test_conn->connect_error) {
        $conn = $test_conn;
        $port = $p;
        break;
    }
    
    // Attempt connecting without selecting a DB (in case SHABD_SANCHAY is not created yet)
    $root_conn = @new mysqli($servername, $username, $password, "", $p);
    if (!$root_conn->connect_error) {
        $root_conn->query("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $root_conn->close();
        
        $conn = @new mysqli($servername, $username, $password, $dbname, $p);
        if (!$conn->connect_error) {
            $port = $p;
            break;
        }
    }
}

if (!$conn) {
    $conn = @new mysqli($servername, $username, $password, $dbname, $port);
}

if ($conn->connect_error) {
    die("
    <!DOCTYPE html>
    <html lang='hi'>
    <head>
        <meta charset='UTF-8'>
        <title>डेटाबेस कनेक्शन त्रुटि - शब्द संचय</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; padding: 40px 20px; display: flex; justify-content: center; }
            .error-card { background: white; max-width: 600px; width: 100%; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 5px solid #e74c3c; padding: 30px; }
            h2 { color: #e74c3c; margin-top: 0; }
            .detail-box { background: #fdf2f2; border: 1px solid #f8b4b4; padding: 12px 15px; border-radius: 6px; color: #9b1c1c; font-family: monospace; font-size: 14px; margin: 15px 0; }
            ol { padding-left: 20px; line-height: 1.8; color: #374151; }
            .code-pill { background: #e5e7eb; padding: 2px 6px; border-radius: 4px; font-family: monospace; color: #1f2937; }
            .btn { display: inline-block; background: #3b82f6; color: white; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: 500; margin-top: 10px; }
            .btn:hover { background: #2563eb; }
        </style>
    </head>
    <body>
        <div class='error-card'>
            <h2>डेटाबेस कनेक्शन त्रुटि (Database Connection Issue)</h2>
            <p><strong>शब्द संचय (Shabd Sanchay)</strong> MySQL डेटाबेस से कनेक्ट नहीं हो सका।</p>
            
            <div class='detail-box'>
                " . htmlspecialchars($conn->connect_error) . "
            </div>

            <h3>आसान समाधान (Quick Fix):</h3>
            <ol>
                <li>यदि आपके MySQL का कोई पासवर्ड है, तो कृपया <span class='code-pill'>config.php</span> में <span class='code-pill'>\$password</span> को अपने पासवर्ड से बदलें।</li>
                <li>डेटाबेस और सैंपल डेटा तैयार करने के लिए <a href='database/setup.php' class='btn'>1-Click Database Setup</a> पर क्लिक करें।</li>
            </ol>
        </div>
    </body>
    </html>
    ");
}

// Set charset to support Hindi Devanagari
$conn->set_charset("utf8mb4");
?>