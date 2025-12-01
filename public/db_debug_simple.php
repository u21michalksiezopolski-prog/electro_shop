<?php
$mysqli = new mysqli('127.0.0.1','root','','electro_shop');
if ($mysqli->connect_errno) {
    echo "Connect failed: " . $mysqli->connect_error;
    exit;
}
$slug = $_GET['slug'] ?? 'iphone-15-pro';
$stmt = $mysqli->prepare('SELECT id, name, slug, image, images FROM products WHERE slug = ? LIMIT 1');
$stmt->bind_param('s', $slug);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
header('Content-Type: text/plain; charset=utf-8');
if (!$row) { echo "NOT FOUND\n"; exit; }
print_r($row);

