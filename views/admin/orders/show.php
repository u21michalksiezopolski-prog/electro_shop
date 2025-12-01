<?php

$userName = 'Guest';
if (!empty($order->user_id)) {
    $user = \App\Models\User::find($order->user_id);
    if ($user) {
        $userName = $user->name ?? 'Guest';
    }
}
?>
<div class="container admin-orders-show">
    <h1>Zamówienie #<?= e($order->id ?? '') ?></h1>
    <a href="<?= url('admin/dashboard') ?>" class="btn">Powrót do panelu</a>
    <p>Użytkownik: <?= e($userName) ?></p>
    <p>Adres: <?= nl2br(e($order->address ?? '')) ?></p>
    <p>Status: <?= e($order->status ?? '') ?> | Płatność: <?= e($order->payment_status ?? '') ?></p>

    <h3>Pozycje</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Produkt</th>
                <th>Ilość</th>
                <th>Cena</th>
                <th>Razem</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): foreach ($items as $it): $p = $it->getProduct(); ?>
                <tr>
                    <td><?= e($p->name ?? 'Produkt') ?></td>
                    <td><?= intval($it->quantity) ?></td>
                    <td><?= number_format($it->price, 2, ',', ' ') ?> zł</td>
                    <td><?= number_format($it->price * $it->quantity, 2, ',', ' ') ?> zł</td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4">Brak pozycji w zamówieniu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p><strong>Razem:</strong> <?= number_format($order->total ?? 0, 2, ',', ' ') ?> zł</p>

    <h3>Aktualizuj status</h3>
    <form action="<?= url('admin/orders/' . ($order->id ?? '') . '/status') ?>" method="POST">
        <?= csrf_field() ?>
        <label>Status zamówienia:</label>
        <select name="status">
            <option value="pending" <?= ($order->status ?? '') === 'pending' ? 'selected' : '' ?>>Oczekujące</option>
            <option value="processing" <?= ($order->status ?? '') === 'processing' ? 'selected' : '' ?>>W realizacji</option>
            <option value="shipped" <?= ($order->status ?? '') === 'shipped' ? 'selected' : '' ?>>Wysłane</option>
            <option value="completed" <?= ($order->status ?? '') === 'completed' ? 'selected' : '' ?>>Zrealizowane</option>
            <option value="cancelled" <?= ($order->status ?? '') === 'cancelled' ? 'selected' : '' ?>>Anulowane</option>
        </select>

        <label>Payment status:</label>
        <select name="payment_status">
            <option value="pending" <?= ($order->payment_status ?? '') === 'pending' ? 'selected' : '' ?>>Oczekująca</option>
            <option value="paid" <?= ($order->payment_status ?? '') === 'paid' ? 'selected' : '' ?>>Opłacona</option>
            <option value="refunded" <?= ($order->payment_status ?? '') === 'refunded' ? 'selected' : '' ?>>Zwrócona</option>
        </select>

        <button class="btn btn-primary">Zapisz</button>
    </form>

    <a href="<?= url('admin/orders') ?>" class="btn">Powrót</a>
</div>
