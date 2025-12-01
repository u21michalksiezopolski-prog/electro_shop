<?php

namespace App\Controllers;

use App\Models\User;

class ProfileController {
    public static function index() {
        if (!auth_check()) {
            redirect('/login');
        }
        
        $user = auth();
        $title = 'Profil';
        $content = __DIR__ . '/../../views/profile.php';
        include __DIR__ . '/../../views/layout.php';
    }
    
    public static function update() {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/profile');
        }
        
        if (!auth_check()) {
            redirect('/login');
        }
        
        $user = auth();
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $new_conf = $_POST['new_password_confirmation'] ?? '';

        if ($new || $current || $new_conf) {
            if (empty($current) || empty($new) || empty($new_conf)) {
                flash('error', 'Wypełnij wszystkie pola zmiany hasła.');
                redirect('/profile');
            }
            if (!password_verify($current, $user->password)) {
                flash('error', 'Aktualne hasło jest nieprawidłowe.');
                redirect('/profile');
            }
            if ($new !== $new_conf) {
                flash('error', 'Nowe hasła nie są identyczne.');
                redirect('/profile');
            }
            if (strlen($new) < 6) {
                flash('error', 'Hasło musi mieć co najmniej 6 znaków.');
                redirect('/profile');
            }

            $user->password = password_hash($new, PASSWORD_DEFAULT);
            $user->save();

            try {
                $subject = 'Twoje hasło zostało zmienione';
                $body = "Witaj " . ($user->name ?? '') . ",\n\n";
                $body .= "Potwierdzamy, że Twoje hasło w serwisie " . (defined('APP_NAME') ? APP_NAME : 'naszym serwisie') . " zostało zmienione.\n";
                $body .= "Jeśli to nie Ty zmieniłeś hasła, natychmiast skontaktuj się z obsługą: " . (defined('MAIL_FROM') ? MAIL_FROM : 'support@localhost') . "\n\n";
                $body .= "Jeżeli chcesz, możesz zabezpieczyć swoje konto dodatkowo poprzez zmianę hasła i włączenie dodatkowych zabezpieczeń.\n\n";
                $body .= "Pozdrawiamy,\n" . (defined('APP_NAME') ? APP_NAME : 'Zespół');

                if (!empty($user->email)) {
                    send_mail($user->email, $subject, $body);
                }
            } catch (\Throwable $e) {
                error_log('Password change email failed: ' . $e->getMessage());
            }

            flash('success', 'Hasło zostało zmienione.');
            redirect('/profile');
        }

        $user->name = $name;
        $user->phone = $phone;
        $user->save();

        flash('success', 'Profil zaktualizowany.');
        redirect('/profile');
    }

    public static function orders() {
        if (!auth_check()) {
            redirect('/login');
        }

        $user = auth();
        $orders = \DB::select("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$user->id]);

        $title = 'Moje zamówienia';
        $content = __DIR__ . '/../../views/profile_orders.php';
        include __DIR__ . '/../../views/layout.php';
    }

    public static function orderShow($id) {
        if (!auth_check()) {
            redirect('/login');
        }

        $user = auth();
        $order = \App\Models\Order::find($id);
        if (!$order || $order->user_id != $user->id) {
            flash('error', 'Zamówienie nie zostało znalezione lub brak dostępu.');
            redirect('/profile/orders');
        }

        $items = $order->getItems();
        $title = 'Zamówienie #' . ($order->id ?? '');
        $content = __DIR__ . '/../../views/profile_order_show.php';
        include __DIR__ . '/../../views/layout.php';
    }

}
