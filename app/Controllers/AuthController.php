<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    public static function showLogin() {
        $title = 'Logowanie';
        $content = __DIR__ . '/../../views/auth/login.php';
        include __DIR__ . '/../../views/layout.php';
    }
    
    public static function login() {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/login');
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = User::authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user'] = $user->toArray();
            flash('success', 'Zalogowano pomyślnie.');
            redirect('/');
        } else {
            flash('error', 'Nieprawidłowe dane logowania.');
            redirect('/login');
        }
    }
    
    public static function showRegister() {
        $title = 'Rejestracja';
        $content = __DIR__ . '/../../views/auth/register.php';
        include __DIR__ . '/../../views/layout.php';
    }
    
    public static function register() {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/register');
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirmation = $_POST['password_confirmation'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        if ($password !== $password_confirmation) {
            flash('error', 'Hasła nie są identyczne.');
            redirect('/register');
        }
        
        if (User::findByEmail($email)) {
            flash('error', 'Email już istnieje.');
            redirect('/register');
        }
        
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone,
            'role' => 'customer'
        ]);
        
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user'] = $user->toArray();
        flash('success', 'Konto zostało utworzone pomyślnie!');
        redirect('/');
    }
    
    public static function logout() {
        session_destroy();
        redirect('/');
    }
    
    public static function showForgotPassword() {
        $title = 'Resetowanie hasła';
        $content = __DIR__ . '/../../views/auth/forgot-password.php';
        include __DIR__ . '/../../views/layout.php';
    }

    public static function forgotPassword() {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/forgot-password');
        }

        $email = $_POST['email'] ?? '';
        $user = User::findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $hashed = password_hash($token, PASSWORD_DEFAULT);
            \DB::insert(
                "INSERT INTO password_reset_tokens (email, token, created_at) VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE token = ?, created_at = NOW()",
                [$email, $hashed, $hashed]
            );

            $resetLink = full_url('reset-password') . '?email=' . urlencode($email) . '&token=' . urlencode($token);
            $subject = 'Resetowanie hasła';
            $body = "Aby zresetować hasło kliknij w link: " . $resetLink;
            send_mail($email, $subject, $body);

            flash('success', 'Link do resetowania hasła został wysłany na adres email.');
        } else {
            flash('error', 'Nie znaleziono użytkownika z tym adresem email.');
        }
        redirect('/forgot-password');
    }

    public static function showResetPassword() {
        $email = $_GET['email'] ?? '';
        $token = $_GET['token'] ?? '';

        $title = 'Ustaw nowe hasło';
        $content = __DIR__ . '/../../views/auth/reset-password.php';
        include __DIR__ . '/../../views/layout.php';
    }

    public static function resetPassword() {
        if (!csrf_verify()) {
            flash('error', 'Błąd bezpieczeństwa.');
            redirect('/reset-password');
        }

        $email = $_POST['email'] ?? '';
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirmation = $_POST['password_confirmation'] ?? '';

        if ($password !== $password_confirmation) {
            flash('error', 'Hasła nie są identyczne.');
            redirect('/reset-password?email=' . urlencode($email) . '&token=' . urlencode($token));
        }

        $record = \DB::selectOne("SELECT * FROM password_reset_tokens WHERE email = ?", [$email]);
        if (!$record) {
            flash('error', 'Token resetowania nie został znaleziony.');
            redirect('/forgot-password');
        }

        $hashed = $record['token'];
        if (!password_verify($token, $hashed)) {
            flash('error', 'Nieprawidłowy token resetowania.');
            redirect('/forgot-password');
        }

        $createdAt = $record['created_at'] ?? null;
        if ($createdAt) {
            $age = time() - strtotime($createdAt);
            if ($age > 3600) {
                flash('error', 'Token wygasł. Poproś o wygenerowanie nowego.');
                redirect('/forgot-password');
            }
        }

        $user = User::findByEmail($email);
        if (!$user) {
            flash('error', 'Nie znaleziono użytkownika.');
            redirect('/forgot-password');
        }

        \DB::update("UPDATE users SET password = ? WHERE email = ?", [password_hash($password, PASSWORD_DEFAULT), $email]);
        \DB::delete("DELETE FROM password_reset_tokens WHERE email = ?", [$email]);

        flash('success', 'Hasło zostało zresetowane. Możesz się teraz zalogować.');
        redirect('/login');
    }
}
