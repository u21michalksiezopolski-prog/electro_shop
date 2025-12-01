<?php

namespace App\Controllers;

use App\Models\Product;

class ProductController {
    public static function show($slug) {
        $product = Product::findBySlug($slug);
        
        if (!$product) {
            http_response_code(404);
            $title = 'Nie znaleziono produktu';
            $content = __DIR__ . '/../../views/404.php';
            include __DIR__ . '/../../views/layout.php';
            return;
        }
        
        \DB::update("UPDATE products SET views = views + 1 WHERE id = ?", [$product->id]);
        $product->views = ($product->views ?? 0) + 1;
        
        $relatedProducts = \DB::select(
            "SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 LIMIT 4",
            [$product->category_id, $product->id]
        );
        $relatedProducts = array_map(function($row) {
            return new Product($row);
        }, $relatedProducts);
        
        $title = $product->name ?? 'Produkt';
        $content = __DIR__ . '/../../views/product.php';
        include __DIR__ . '/../../views/layout.php';
    }
}
