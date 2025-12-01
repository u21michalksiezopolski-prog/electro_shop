<div class="container employee-order-show">
    <h1>Zamówienie #<?= e($order->id ?? '') ?></h1>
    <p>Użytkownik: <?= e($order->name ?? $order->email ?? 'Guest') ?></p>
    <p>Adres: <?= nl2br(e($order->address ?? '')) ?></p>

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
                <tr><td colspan="4">Brak pozycji.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p><strong>Razem:</strong> <?= number_format($order->total ?? 0, 2, ',', ' ') ?> zł</p>

    <a href="<?= url('employee/orders') ?>" class="btn">Powrót</a>
</div>

