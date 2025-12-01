<?php

namespace App\Models;

class Favorite extends Model {
    protected $table = 'favorites';
    
    protected $fillable = ['user_id', 'product_id'];
    
    public function getProduct() {
        return Product::find($this->product_id);
    }
    
    public static function getByUser($userId) {
        $results = \DB::select("SELECT * FROM favorites WHERE user_id = ?", [$userId]);
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }
    
    public static function exists($userId, $productId) {
        $result = \DB::selectOne(
            "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
        return $result !== null;
    }
}
