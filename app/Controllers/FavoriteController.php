<?php

namespace App\Controllers;

use App\Models\Favorite;
use App\Models\Product;

class FavoriteController {
    public static function index() {
        if (!auth_check()) {
            redirect('/login');
        }
        
        $favorites = Favorite::getByUser($_SESSION['user_id']);
        
        $title = 'Ulubione';
        $content = __DIR__ . '/../../views/favorites.php';
        include __DIR__ . '/../../views/layout.php';
    }
    
    public static function toggle($productId) {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/');
        }
        
        if (!auth_check()) {
            flash('error', 'Musisz być zalogowany.');
            redirect('/login');
        }
        
        $userId = $_SESSION['user_id'];
        
        if (Favorite::exists($userId, $productId)) {
            $favorite = \DB::selectOne(
                "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?",
                [$userId, $productId]
            );
            if ($favorite) {
                \DB::delete("DELETE FROM favorites WHERE id = ?", [$favorite['id']]);
                flash('success', 'Produkt usunięty z ulubionych.');
            }
        } else {
            Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            flash('success', 'Produkt dodany do ulubionych.');
        }
        
        redirect('/product/' . Product::find($productId)->slug);
    }
}
