<div class="container my-orders-page">
    <h1>Moje zamówienia</h1>
    <?php if (empty($orders)): ?>
        <p>Brak zamówień.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($orders as $o): ?>
                <li><a href="<?= url('orders/' . $o->id) ?>">Zamówienie #<?= e($o->order_number) ?> — <?= number_format($o->total, 2, ',', ' ') ?> zł</a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

