<?php
// views/admin/dashboard.php
// Oczekiwane zmienne: $stats, $recentOrders
?>
<div class="container admin-dashboard">
    <h1>Panel Admina</h1>
    <div style="margin-bottom:12px;">
        <a href="<?= url('admin/orders') ?>" class="btn btn-primary">Zobacz zamówienia</a>
        <a href="<?= url('admin/products') ?>" class="btn">Zarządzaj produktami</a>
        <a href="<?= url('admin/users') ?>" class="btn">Zarządzaj użytkownikami</a>
    </div>
    <div class="admin-stats">
        <div>Wszystkie zamówienia: <?= intval($stats['total_orders'] ?? 0) ?></div>
        <div>Oczekujące: <?= intval($stats['pending_orders'] ?? 0) ?></div>
        <div>Produkty: <?= intval($stats['total_products'] ?? 0) ?></div>
        <div>Użytkownicy: <?= intval($stats['total_users'] ?? 0) ?></div>
    </div>
    <h3>Najnowsze zamówienia</h3>
    <ul>
        <?php foreach ($recentOrders as $ro): ?>
            <li><a href="<?= url('admin/orders/' . ($ro['id'] ?? '')) ?>">Zamówienie #<?= e($ro['id'] ?? '') ?> od <?= e($ro['user_name'] ?? 'Guest') ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
