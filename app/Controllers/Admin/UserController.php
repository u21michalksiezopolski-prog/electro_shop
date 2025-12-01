<?php

namespace App\Controllers\Admin;

use App\Models\User;

class UserController {
    public static function index() {
        if (!auth_check() || !auth()->isAdmin()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }
        
        $users = User::all();
        $title = 'Użytkownicy';
        $content = __DIR__ . '/../../../views/admin/users/index.php';
        include __DIR__ . '/../../../views/layout.php';
    }

    public static function changeRole($id) {
        if (!csrf_verify() || !auth_check() || !auth()->isAdmin()) {
            flash('error', 'Brak dostępu.');
            redirect('/');
        }

        $user = User::find($id);
        if (!$user) {
            flash('error', 'Użytkownik nie został znaleziony.');
            redirect('/admin/users');
        }

        $role = $_POST['role'] ?? 'customer';
        $allowed = ['customer', 'employee', 'admin'];
        if (!in_array($role, $allowed)) {
            flash('error', 'Nieprawidłowa rola.');
            redirect('/admin/users');
        }

        // Prevent demoting the last admin: ensure at least one other admin remains
        if ($user->role === 'admin' && $role !== 'admin') {
            $admins = \DB::select("SELECT COUNT(*) as c FROM users WHERE role = 'admin'")[0]['c'] ?? 0;
            if ($admins <= 1) {
                flash('error', 'Nie można usunąć roli ostatniego administratora.');
                redirect('/admin/users');
            }
        }

        $user->role = $role;
        $user->save();
        flash('success', 'Rola użytkownika została zaktualizowana.');
        redirect('/admin/users');
    }
}
