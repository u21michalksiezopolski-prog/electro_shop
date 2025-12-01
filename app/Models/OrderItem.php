<?php

namespace App\Models;

class OrderItem extends Model {
    protected $table = 'order_items';
    
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'product_sku',
        'price', 'quantity', 'total'
    ];
    
    public static function getByOrder($orderId) {
        $results = \DB::select("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }

    // Return product model (if exists) or a lightweight Product-like object constructed from stored item data
    public function getProduct() {
        // Try to load real product by id
        if (!empty($this->product_id)) {
            $prod = Product::find($this->product_id);
            if ($prod) return $prod;
        }

        // Fallback: create a minimal Product instance using stored snapshot fields
        $data = [
            'id' => null,
            'name' => $this->product_name ?? null,
            'sku' => $this->product_sku ?? null,
            'price' => $this->price ?? null,
        ];
        return new Product($data);
    }
}
