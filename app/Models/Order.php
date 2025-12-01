<?php

namespace App\Models;

class Order extends Model {
    protected $table = 'orders';
    
    protected $fillable = [
        'order_number', 'user_id', 'email', 'name', 'phone', 'address',
        'city', 'postal_code', 'subtotal', 'tax', 'shipping', 'total',
        'status', 'payment_status', 'payment_method', 'notes'
    ];
    
    public static function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
    
    public function getItems() {
        return OrderItem::getByOrder($this->id);
    }
    
    public static function getByUser($userId) {
        $results = \DB::select("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
        return array_map(function($row) {
            return new static($row);
        }, $results);
    }
}
