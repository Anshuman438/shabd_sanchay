<?php
// CSS Minifier Script - run once to generate style.min.css
$input = file_get_contents(__DIR__ . '/css/style.css');

// Remove comments
$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $input);
// Remove whitespace before and after special characters
$css = preg_replace('/\s*{\s*/', '{', $css);
$css = preg_replace('/\s*}\s*/', '}', $css);
$css = preg_replace('/\s*:\s*/', ':', $css);
$css = preg_replace('/\s*;\s*/', ';', $css);
$css = preg_replace('/\s*,\s*/', ',', $css);
// Remove spaces around selectors
$css = preg_replace('/\s*>\s*/', '>', $css);
$css = preg_replace('/\s*~\s*/', '~', $css);
$css = preg_replace('/\s*\+\s*/', '+', $css);
// Remove multiple whitespace
$css = preg_replace('/\s+/', ' ', $css);
// Remove leading/trailing whitespace
$css = trim($css);
// Fix: space after colons only when NOT in URL/data
// Remove trailing semicolons before }
$css = str_replace(';}', '}', $css);
// Remove empty rules
$css = preg_replace('/[^\{\}]+\{\}/', '', $css);

$output_path = __DIR__ . '/css/style.min.css';
file_put_contents($output_path, $css);

$orig_size = strlen($input);
$min_size = strlen($css);
$saved = round((1 - $min_size/$orig_size)*100, 1);
echo "Original: " . number_format($orig_size) . " bytes\n";
echo "Minified: " . number_format($min_size) . " bytes\n";
echo "Saved: {$saved}%\n";
echo "Written to: $output_path\n";
