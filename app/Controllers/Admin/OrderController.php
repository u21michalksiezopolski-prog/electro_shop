<?php

namespace App\Controllers\Admin;

use App\Models\Order;

class OrderController {
    public static function index() {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $orders = \DB::select("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        $title = 'Zamówienia - Panel Admina';
        $content = __DIR__ . '/../../../views/admin/orders/index.php';
        include __DIR__ . '/../../../views/layout.php';
    }
    
    public static function show($id) {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $order = Order::find($id);
        if (!$order) {
            flash('error', 'Zamówienie nie zostało znalezione.');
            redirect('/admin/orders');
        }
        
        $items = $order->getItems();
        $title = 'Zamówienie #' . ($order->id ?? '');
        $content = __DIR__ . '/../../../views/admin/orders/show.php';
        include __DIR__ . '/../../../views/layout.php';
    }
    
    public static function updateStatus($id) {
        if (!csrf_verify() || !auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $order = Order::find($id);
        if ($order) {
            $order->status = $_POST['status'] ?? 'pending';
            $order->payment_status = $_POST['payment_status'] ?? 'pending';
            $order->save();
            flash('success', 'Status zamówienia został zaktualizowany.');
        }
        redirect('/admin/orders/' . $id);
    }
}
