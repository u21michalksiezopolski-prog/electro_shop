<?php

namespace App\Models;

class Cart extends Model {
    protected $table = 'cart';
    
    protected $fillable = ['session_id', 'user_id', 'product_id', 'quantity'];
    
    public function getProduct() {
        return Product::find($this->product_id);
    }
    
    public function getTotal() {
        $product = $this->getProduct();
        return $product ? $this->quantity * $product->price : 0;
    }
    
    public static function getBySession($sessionId) {
        $results = \DB::select("SELECT * FROM cart WHERE session_id = ?", [$sessionId]);
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }
    
    public static function getByUser($userId) {
        $results = \DB::select("SELECT * FROM cart WHERE user_id = ?", [$userId]);
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }
}
