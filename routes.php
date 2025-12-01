<?php

return [
    // Strona główna
    'GET:/' => function() {
        \App\Controllers\HomeController::index();
    },
    
    // Produkty
    'GET:/product/{slug}' => function($slug) {
        \App\Controllers\ProductController::show($slug);
    },
    
    // Autentykacja
    'GET:/login' => function() {
        \App\Controllers\AuthController::showLogin();
    },
    'POST:/login' => function() {
        \App\Controllers\AuthController::login();
    },
    'GET:/register' => function() {
        \App\Controllers\AuthController::showRegister();
    },
    'POST:/register' => function() {
        \App\Controllers\AuthController::register();
    },
    'POST:/logout' => function() {
        \App\Controllers\AuthController::logout();
    },
    'GET:/forgot-password' => function() {
        \App\Controllers\AuthController::showForgotPassword();
    },
    'POST:/forgot-password' => function() {
        \App\Controllers\AuthController::forgotPassword();
    },
    // Password reset (link z email)
    'GET:/reset-password' => function() {
        \App\Controllers\AuthController::showResetPassword();
    },
    'POST:/reset-password' => function() {
        \App\Controllers\AuthController::resetPassword();
    },

    // Koszyk
    'GET:/cart' => function() {
        \App\Controllers\CartController::index();
    },
    'POST:/cart/add/{id}' => function($id) {
        \App\Controllers\CartController::add($id);
    },
    'POST:/cart/update/{id}' => function($id) {
        \App\Controllers\CartController::update($id);
    },
    'POST:/cart/remove/{id}' => function($id) {
        \App\Controllers\CartController::remove($id);
    },
    
    // Zamówienia
    'GET:/checkout' => function() {
        \App\Controllers\OrderController::checkout();
    },
    'POST:/orders' => function() {
        \App\Controllers\OrderController::store();
    },
    'GET:/orders/{id}' => function($id) {
        \App\Controllers\OrderController::show($id);
    },
    'GET:/my-orders' => function() {
        \App\Controllers\OrderController::myOrders();
    },
    
    // Profil
    'GET:/profile' => function() {
        \App\Controllers\ProfileController::index();
    },
    'POST:/profile' => function() {
        \App\Controllers\ProfileController::update();
    },
    // Profile - order history
    'GET:/profile/orders' => function() {
        \App\Controllers\ProfileController::orders();
    },
    'GET:/profile/orders/{id}' => function($id) {
        \App\Controllers\ProfileController::orderShow($id);
    },

    // Ulubione
    'GET:/favorites' => function() {
        \App\Controllers\FavoriteController::index();
    },
    'POST:/favorites/toggle/{id}' => function($id) {
        \App\Controllers\FavoriteController::toggle($id);
    },
    
    // Admin
    'GET:/admin/dashboard' => function() {
        \App\Controllers\AdminController::dashboard();
    },
    'GET:/admin/products' => function() {
        \App\Controllers\Admin\ProductController::index();
    },
    'GET:/admin/products/create' => function() {
        \App\Controllers\Admin\ProductController::create();
    },
    'POST:/admin/products' => function() {
        \App\Controllers\Admin\ProductController::store();
    },
    'GET:/admin/products/{id}/edit' => function($id) {
        \App\Controllers\Admin\ProductController::edit($id);
    },
    'POST:/admin/products/{id}' => function($id) {
        \App\Controllers\Admin\ProductController::update($id);
    },
    'POST:/admin/products/{id}/delete' => function($id) {
        \App\Controllers\Admin\ProductController::delete($id);
    },
    'GET:/admin/orders' => function() {
        \App\Controllers\Admin\OrderController::index();
    },
    'GET:/admin/orders/{id}' => function($id) {
        \App\Controllers\Admin\OrderController::show($id);
    },
    'POST:/admin/orders/{id}/status' => function($id) {
        \App\Controllers\Admin\OrderController::updateStatus($id);
    },
    'GET:/admin/users' => function() {
        \App\Controllers\Admin\UserController::index();
    },
    'POST:/admin/users/{id}/role' => function($id) {
        \App\Controllers\Admin\UserController::changeRole($id);
    },
    // pracownik panel
    'GET:/employee/dashboard' => function() {
        \App\Controllers\EmployeeController::dashboard();
    },
    'GET:/employee/orders' => function() {
        \App\Controllers\EmployeeController::orders();
    },
    'GET:/employee/orders/{id}' => function($id) {
        \App\Controllers\EmployeeController::show($id);
    },
];
