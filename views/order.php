<div class="container order-page">
    <h1>Szczegóły zamówienia #<?= e($order->order_number ?? $order->id) ?></h1>
    <p>Status: <?= e($order->status ?? '') ?></p>
    <h3>Pozycje</h3>
    <ul>
        <?php foreach ($items as $it): ?>
            <li><?= e($it->product_name) ?> x <?= intval($it->quantity) ?> — <?= number_format($it->total, 2, ',', ' ') ?> zł</li>
        <?php endforeach; ?>
    </ul>
    <p><strong>Razem: <?= number_format($order->total ?? 0, 2, ',', ' ') ?> zł</strong></p>
</div>

