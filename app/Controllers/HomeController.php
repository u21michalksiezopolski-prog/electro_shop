<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController {
    public static function index() {
        $sql = "SELECT * FROM products WHERE is_active = 1";
        $params = [];
        
        if (!empty($_GET['category'])) {
            $sql .= " AND category_id = ?";
            $params[] = $_GET['category'];
        }
        
        if (!empty($_GET['brand'])) {
            $sql .= " AND brand = ?";
            $params[] = $_GET['brand'];
        }
        
        if (!empty($_GET['min_price'])) {
            $sql .= " AND price >= ?";
            $params[] = $_GET['min_price'];
        }
        
        if (!empty($_GET['max_price'])) {
            $sql .= " AND price <= ?";
            $params[] = $_GET['max_price'];
        }
        
        $sort = $_GET['sort'] ?? 'latest';
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY price DESC";
                break;
            case 'name':
                $sql .= " ORDER BY name ASC";
                break;
            default:
                $sql .= " ORDER BY created_at DESC";
        }
        
        $sql .= " LIMIT 12";
        
        $results = \DB::select($sql, $params);
        $products = array_map(function($row) {
            return new Product($row);
        }, $results);
        $categories = Category::getActive();
        
        $brands = \DB::select("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' AND is_active = 1");
        $brands = array_column($brands, 'brand');
        
        $title = 'Strona główna';
        $content = __DIR__ . '/../../views/home.php';
        include __DIR__ . '/../../views/layout.php';
    }
}
