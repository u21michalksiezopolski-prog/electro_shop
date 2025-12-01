<?php
require_once __DIR__ . '/../config.php';
$slug = $_GET['slug'] ?? 'iphone-15-pro';
$image = $_GET['image'] ?? '';
$images = $_GET['images'] ?? '';
if (empty($image) && empty($images)) {
    echo "Provide image=... or images=...\n";
    exit;
}
if ($images) {
    $arr = array_map('trim', explode(',', $images));
    $json = json_encode($arr);
} else {
    $json = null;
}
\DB::update('UPDATE products SET image = ?, images = ? WHERE slug = ?', [$image ?: null, $json, $slug]);
echo "Updated $slug with image=$image and images=$json\n";

