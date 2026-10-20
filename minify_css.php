<?php
// =========================================================================
// SHABD SANCHAY: CSS BUILD & MINIFIER ENGINE
// Minifies all modular CSS files and the consolidated style.min.css
// =========================================================================

function minifyCSS($css) {
    // Strip comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // Remove whitespace around brackets, colons, semicolons, commas
    $css = preg_replace('/\s*([\{\};:,>~+])\s*/', '$1', $css);
    // Clean remaining excess whitespace
    $css = preg_replace('/\s+/', ' ', $css);
    // Trailing semicolons before closing brace
    $css = str_replace(';}', '}', $css);
    // Remove empty rules
    $css = preg_replace('/[^\{\}]+\{\}/', '', $css);
    return trim($css);
}

$modulesDir = __DIR__ . '/css/modules';
$cssDir = __DIR__ . '/css';

$modules = [
    'common'  => $modulesDir . '/common.css',
    'home'    => $modulesDir . '/home.css',
    'catalog' => $modulesDir . '/catalog.css',
    'reading' => $modulesDir . '/reading.css',
    'learn'   => $modulesDir . '/learn.css',
    'pages'   => $modulesDir . '/pages.css',
];

echo "\n======================================================\n";
echo "   SHABD SANCHAY - CSS MINIFICATION REPORT\n";
echo "======================================================\n\n";

$masterCSS = '';

foreach ($modules as $name => $filePath) {
    if (!file_exists($filePath)) continue;
    $raw = file_get_contents($filePath);
    $masterCSS .= "\n\n" . $raw;
    $min = minifyCSS($raw);
    file_put_contents($cssDir . '/' . $name . '.min.css', $min);

    $rawKB = round(strlen($raw) / 1024, 1);
    $minKB = round(strlen($min) / 1024, 1);
    $gzKB  = round(strlen(gzencode($min, 9)) / 1024, 1);
    $saved = round((1 - strlen($min) / strlen($raw)) * 100, 1);

    echo str_pad(strtoupper($name) . ' Module', 18)
       . " => Raw: " . str_pad($rawKB . ' KB', 9)
       . " | Min: " . str_pad($minKB . ' KB', 9)
       . " | Gzip: " . str_pad($gzKB . ' KB', 8)
       . " | Saved: {$saved}%\n";
}

// Master style.min.css
$masterMin = minifyCSS($masterCSS);
file_put_contents($cssDir . '/style.min.css', $masterMin);
file_put_contents($cssDir . '/style.css', trim($masterCSS));

$masterRawKB = round(strlen($masterCSS) / 1024, 1);
$masterMinKB = round(strlen($masterMin) / 1024, 1);
$masterGzKB  = round(strlen(gzencode($masterMin, 9)) / 1024, 1);
$masterSaved = round((1 - strlen($masterMin) / strlen($masterCSS)) * 100, 1);

echo "------------------------------------------------------\n";
echo str_pad('MASTER (All-in-One)', 18)
   . " => Raw: " . str_pad($masterRawKB . ' KB', 9)
   . " | Min: " . str_pad($masterMinKB . ' KB', 9)
   . " | Gzip: " . str_pad($masterGzKB . ' KB', 8)
   . " | Saved: {$masterSaved}%\n";
echo "======================================================\n";
echo "Build completed successfully.\n\n";
