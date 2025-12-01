<div class="container profile-orders">
    <h1>Moje zamówienia</h1>
    <?php if (empty($orders)): ?>
        <p>Nie masz jeszcze zamówień.</p>
        <a href="<?= url() ?>" class="btn">Kontynuuj zakupy</a>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Kwota</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= e($o['id']) ?></td>
                        <td><?= e($o['created_at']) ?></td>
                        <td><?= number_format($o['total'] ?? 0, 2, ',', ' ') ?> zł</td>
                        <td><?= e($o['status'] ?? '') ?> / <?= e($o['payment_status'] ?? '') ?></td>
                        <td><a href="<?= url('profile/orders/' . $o['id']) ?>" class="btn">Szczegóły</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

