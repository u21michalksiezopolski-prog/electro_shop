<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminController {
    public static function dashboard() {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $stats = [
            'total_orders' => \DB::selectOne("SELECT COUNT(*) as count FROM orders")['count'] ?? 0,
            'pending_orders' => \DB::selectOne("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")['count'] ?? 0,
            'total_products' => \DB::selectOne("SELECT COUNT(*) as count FROM products")['count'] ?? 0,
            'low_stock' => \DB::selectOne("SELECT COUNT(*) as count FROM products WHERE stock < 10")['count'] ?? 0,
            'total_users' => \DB::selectOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")['count'] ?? 0,
            'total_revenue' => \DB::selectOne("SELECT SUM(total) as total FROM orders WHERE payment_status = 'paid'")['total'] ?? 0
        ];
        
        $recentOrders = \DB::select("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10");
        
        $title = 'Panel Admina';
        $content = __DIR__ . '/../../views/admin/dashboard.php';
        include __DIR__ . '/../../views/layout.php';
    }
}
