<?php

namespace App\Models;

class User extends Model {
    protected $table = 'users';
    
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 
        'city', 'postal_code', 'role'
    ];
    
    public function isAdmin() {
        return $this->role === 'admin';
    }
    
    public function isEmployee() {
        return $this->role === 'employee' || $this->role === 'admin';
    }
    
    public static function findByEmail($email) {
        $result = \DB::selectOne("SELECT * FROM users WHERE email = ?", [$email]);
        return $result ? new static($result) : null;
    }
    
    public static function authenticate($email, $password) {
        $user = static::findByEmail($email);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }
}
