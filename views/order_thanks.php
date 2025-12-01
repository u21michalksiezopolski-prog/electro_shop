<div class="container order-thanks">
    <h1>Dziękujemy za zamówienie</h1>
    <p>Twoje zamówienie numer <strong>#<?= e($order->order_number ?? $order->id) ?></strong> zostało złożone pomyślnie.</p>
    <h3>Szczegóły zamówienia</h3>
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
            <?php foreach ($items as $it): $p = $it->getProduct(); ?>
                <tr>
                    <td><?= e($p->name ?? $it->product_name ?? 'Produkt') ?></td>
                    <td><?= intval($it->quantity) ?></td>
                    <td><?= number_format($it->price, 2, ',', ' ') ?> zł</td>
                    <td><?= number_format($it->total, 2, ',', ' ') ?> zł</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><strong>Razem:</strong> <?= number_format($order->total ?? 0, 2, ',', ' ') ?> zł</p>
    <p>Na adres email: <?= e($order->email ?? '') ?></p>
    <a href="<?= url() ?>" class="btn">Kontynuuj zakupy</a>
</div>

