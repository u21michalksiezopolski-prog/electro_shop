<?php

namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;

class ProductController {
    public static function index() {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $products = Product::all();
        $title = 'Zarządzaj produktami';
        $content = __DIR__ . '/../../../views/admin/products/index.php';
        include __DIR__ . '/../../../views/layout.php';
    }
    
    public static function create() {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $categories = Category::getActive();
        $title = 'Dodaj produkt';
        $content = __DIR__ . '/../../../views/admin/products/create.php';
        include __DIR__ . '/../../../views/layout.php';
    }
    
    public static function store() {
        if (!csrf_verify() || !auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'] ?? '')));

        Product::create([
            'name' => $_POST['name'] ?? '',
            'slug' => $slug,
            'description' => $_POST['description'] ?? '',
            'short_description' => $_POST['short_description'] ?? '',
            'price' => floatval($_POST['price'] ?? 0),
            'old_price' => !empty($_POST['old_price']) ? floatval($_POST['old_price']) : null,
            'stock' => intval($_POST['stock'] ?? 0),
            'sku' => $_POST['sku'] ?? '',
            'category_id' => intval($_POST['category_id'] ?? 0),
            'brand' => $_POST['brand'] ?? '',
            'image' => !empty($_POST['image_url']) ? trim($_POST['image_url']) : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0
        ]);

        flash('success', 'Produkt został utworzony.');
        redirect('/admin/products');
    }
    
    public static function edit($id) {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $product = Product::find($id);
        $categories = Category::getActive();
        $title = 'Edytuj produkt';
        $content = __DIR__ . '/../../../views/admin/products/edit.php';
        include __DIR__ . '/../../../views/layout.php';
    }
    
    public static function update($id) {
        if (!csrf_verify() || !auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }

        $product = Product::find($id);
        if (!$product) {
            flash('error', 'Produkt nie został znaleziony.');
            redirect('/admin/products');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'] ?? '')));

        $product->name = $_POST['name'] ?? '';
        $product->slug = $slug;
        $product->description = $_POST['description'] ?? '';
        $product->short_description = $_POST['short_description'] ?? '';
        $product->price = floatval($_POST['price'] ?? 0);
        $product->old_price = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : null;
        $product->stock = intval($_POST['stock'] ?? 0);
        $product->sku = $_POST['sku'] ?? '';
        $product->category_id = intval($_POST['category_id'] ?? 0);
        $product->brand = $_POST['brand'] ?? '';
        // Do not overwrite image with null here. Respect posted image_url if provided.
        $product->is_active = isset($_POST['is_active']) ? 1 : 0;
        $product->is_featured = isset($_POST['is_featured']) ? 1 : 0;

        // If admin provided image_url, set it (or keep existing if not provided)
        if (!empty($_POST['image_url'])) {
            $product->image = trim($_POST['image_url']);
        }

        $product->save();

        flash('success', 'Produkt został zaktualizowany.');
        redirect('/admin/products');
    }
    
    public static function delete($id) {
        if (!csrf_verify() || !auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            flash('success', 'Produkt został usunięty.');
        }
        redirect('/admin/products');
    }
}
