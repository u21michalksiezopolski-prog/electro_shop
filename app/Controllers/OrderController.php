<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;

class OrderController {
    private static function getCartItems() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = session_id();
        
        if ($userId) {
            return Cart::getByUser($userId);
        } else {
            return Cart::getBySession($sessionId);
        }
    }
    
    public static function checkout() {
        $cartItems = self::getCartItems();
        
        if (empty($cartItems)) {
            flash('error', 'Koszyk jest pusty.');
            redirect('/cart');
        }
        
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item->getTotal();
        }
        $tax = $subtotal * 0.23;
        $shipping = 15.00;
        $total = $subtotal + $tax + $shipping;
        
        $title = 'Podsumowanie zamówienia';
        $content = __DIR__ . '/../../views/checkout.php';
        include __DIR__ . '/../../views/layout.php';
    }
    
    public static function store() {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/checkout');
        }
        
        $cartItems = self::getCartItems();
        if (empty($cartItems)) {
            flash('error', 'Koszyk jest pusty.');
            redirect('/cart');
        }
        
        $email = $_POST['email'] ?? '';
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        $postal_code = $_POST['postal_code'] ?? '';
        $payment_method = $_POST['payment_method'] ?? '';
        
        if (empty($email) || empty($name) || empty($address) || empty($city) || empty($postal_code)) {
            flash('error', 'Wypełnij wszystkie wymagane pola.');
            redirect('/checkout');
        }
        
        \DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item->getTotal();
            }
            $tax = $subtotal * 0.23;
            $shipping = 15.00;
            $total = $subtotal + $tax + $shipping;
            
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $_SESSION['user_id'] ?? null,
                'email' => $email,
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'postal_code' => $postal_code,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => $payment_method,
                'status' => 'pending',
                'payment_status' => 'pending'
            ]);
            
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->getProduct();
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'price' => $product->price,
                    'quantity' => $cartItem->quantity,
                    'total' => $cartItem->getTotal()
                ]);
                
                \DB::update(
                    "UPDATE products SET stock = stock - ? WHERE id = ?",
                    [$cartItem->quantity, $product->id]
                );
                
                $cartItem->delete();
            }
            
            \DB::commit();
            flash('success', 'Zamówienie zostało złożone pomyślnie!');

            try {
                $orderUrl = full_url('orders/' . $order->id);
                $subject = 'Potwierdzenie zamówienia #' . ($order->order_number ?? $order->id);

                $body = "Dzie\xc5\x82\xc5\x84,\n\n";
                $body .= "Dziękujemy za złożenie zamówienia. Poni\xc5\xb9ej znajduj\xc4\x85 się szczegóły:\n\n";
                $body .= "Numer zamówienia: " . ($order->order_number ?? $order->id) . "\n";
                $body .= "Kwota: " . number_format($order->total ?? $total, 2, ',', ' ') . " zł\n";
                $body .= "Szczegóły zamówienia: " . $orderUrl . "\n\n";
                $body .= "Pozdrawiamy,\n" . (defined('APP_NAME') ? APP_NAME : 'Sklep');

                if (!empty($order->email)) {
                    send_mail($order->email, $subject, $body);
                }

                $adminEmail = 'info@electroshop.pl';
                $adminSubject = 'Nowe zamówienie #' . ($order->order_number ?? $order->id);
                $adminBody = "Nowe zamówienie złożone.\n\n" . $body;
                send_mail($adminEmail, $adminSubject, $adminBody);
            } catch (\Throwable $e) {
                error_log('Email sending failed: ' . $e->getMessage());
            }

            if (auth_check()) {
                redirect('/orders/' . $order->id);
            } else {
                $items = OrderItem::getByOrder($order->id);
                $title = 'Dziękujemy za zamówienie';
                $content = __DIR__ . '/../../views/order_thanks.php';
                include __DIR__ . '/../../views/layout.php';
                return;
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            flash('error', 'Wystąpił błąd podczas składania zamówienia.');
            redirect('/checkout');
        }
    }
    
    public static function show($id) {
        $order = Order::find($id);
        
        if (!$order) {
            http_response_code(404);
            $title = 'Nie znaleziono';
            $content = __DIR__ . '/../../views/404.php';
            include __DIR__ . '/../../views/layout.php';
            return;
        }
        
        $user = auth();
        if (!$user || ($order->user_id != $user->id && !$user->isEmployee())) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $items = $order->getItems();
        
        $title = 'Szczegóły zamówienia';
        $content = __DIR__ . '/../../views/order.php';
        include __DIR__ . '/../../views/layout.php';
    }
    
    public static function myOrders() {
        if (!auth_check()) {
            redirect('/login');
        }
        
        $orders = Order::getByUser($_SESSION['user_id']);
        
        $title = 'Moje zamówienia';
        $content = __DIR__ . '/../../views/my-orders.php';
        include __DIR__ . '/../../views/layout.php';
    }
}
