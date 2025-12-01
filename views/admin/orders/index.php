<div class="container admin-orders-index">
    <h1>Zamówienia</h1>
    <a href="<?= url('admin/dashboard') ?>" class="btn">Powrót do panelu</a>

    <table class="admin-table" style="margin-top:16px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Użytkownik</th>
                <th>Kwota</th>
                <th>Status</th>
                <th>Data</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="6">Brak zamówień.</td></tr>
            <?php else: ?>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= e($o['id'] ?? '') ?></td>
                        <td><?= e($o['user_name'] ?? 'Guest') ?></td>
                        <td><?= number_format($o['total'] ?? 0, 2, ',', ' ') ?> zł</td>
                        <td><?= e($o['status'] ?? '') ?> / <?= e($o['payment_status'] ?? '') ?></td>
                        <td><?= e($o['created_at'] ?? '') ?></td>
                        <td>
                            <a href="<?= url('admin/orders/' . ($o['id'] ?? '')) ?>" class="btn">Pokaż</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

