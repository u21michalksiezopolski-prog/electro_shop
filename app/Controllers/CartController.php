<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;

class CartController {
    private static function getCartItems() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = session_id();
        
        if ($userId) {
            return Cart::getByUser($userId);
        } else {
            return Cart::getBySession($sessionId);
        }
    }
    
    public static function index() {
        $cartItems = self::getCartItems();
        $total = 0;
        
        foreach ($cartItems as $item) {
            $total += $item->getTotal();
        }
        
        $title = 'Koszyk';
        $content = __DIR__ . '/../../views/cart.php';
        include __DIR__ . '/../../views/layout.php';
    }
    
    public static function add($productId) {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/');
        }
        
        $product = Product::find($productId);
        if (!$product) {
            flash('error', 'Produkt nie został znaleziony.');
            redirect('/');
        }
        
        $quantity = intval($_POST['quantity'] ?? 1);
        
        if ($quantity > $product->stock) {
            flash('error', 'Nie ma wystarczającej ilości produktu w magazynie.');
            redirect('/product/' . $product->slug);
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = session_id();
        
        if ($userId) {
            $existing = \DB::selectOne(
                "SELECT * FROM cart WHERE user_id = ? AND product_id = ?",
                [$userId, $productId]
            );
        } else {
            $existing = \DB::selectOne(
                "SELECT * FROM cart WHERE session_id = ? AND product_id = ?",
                [$sessionId, $productId]
            );
        }
        
        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            if ($newQuantity > $product->stock) {
                flash('error', 'Nie ma wystarczającej ilości produktu w magazynie.');
                redirect('/product/' . $product->slug);
            }
            \DB::update(
                "UPDATE cart SET quantity = ? WHERE id = ?",
                [$newQuantity, $existing['id']]
            );
        } else {
            \DB::insert(
                "INSERT INTO cart (session_id, user_id, product_id, quantity) VALUES (?, ?, ?, ?)",
                [$userId ? null : $sessionId, $userId, $productId, $quantity]
            );
        }
        
        flash('success', 'Produkt dodany do koszyka.');
        redirect('/product/' . $product->slug);
    }
    
    public static function update($cartId) {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/cart');
        }
        
        $cart = Cart::find($cartId);
        if (!$cart) {
            flash('error', 'Pozycja nie została znaleziona.');
            redirect('/cart');
        }
        
        $quantity = intval($_POST['quantity'] ?? 1);
        $product = $cart->getProduct();
        
        if ($quantity > $product->stock) {
            flash('error', 'Nie ma wystarczającej ilości produktu w magazynie.');
            redirect('/cart');
        }
        
        $cart->quantity = $quantity;
        $cart->save();
        
        flash('success', 'Koszyk zaktualizowany.');
        redirect('/cart');
    }
    
    public static function remove($cartId) {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/cart');
        }
        
        $cart = Cart::find($cartId);
        if ($cart) {
            $cart->delete();
        }
        
        flash('success', 'Produkt usunięty z koszyka.');
        redirect('/cart');
    }
}
