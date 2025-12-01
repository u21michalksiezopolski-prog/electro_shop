<?php

namespace App\Controllers;

class EmployeeController {
    public static function dashboard() {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }

        $ordersCount = \DB::selectOne("SELECT COUNT(*) as c FROM orders WHERE status = 'pending'")['c'] ?? 0;
        $title = 'Panel Pracownika';
        $content = __DIR__ . '/../../views/employee/dashboard.php';
        include __DIR__ . '/../../views/layout.php';
    }

    public static function orders() {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }

        $orders = \DB::select("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.status IN ('pending','processing') ORDER BY o.created_at DESC");
        $title = 'Zamówienia - Panel Pracownika';
        $content = __DIR__ . '/../../views/employee/orders.php';
        include __DIR__ . '/../../views/layout.php';
    }

    public static function show($id) {
        if (!auth_check() || !auth()->isEmployee()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }

        $order = \App\Models\Order::find($id);
        if (!$order) {
            flash('error', 'Zamówienie nie zostało znalezione.');
            redirect('/employee/orders');
        }
        $items = $order->getItems();
        $title = 'Zamówienie #' . ($order->id ?? '');
        $content = __DIR__ . '/../../views/employee/order_show.php';
        include __DIR__ . '/../../views/layout.php';
    }
}
