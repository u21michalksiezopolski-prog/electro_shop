<div class="container employee-orders">
    <h1>Zlecenia do realizacji</h1>
    <?php if (empty($orders)): ?>
        <p>Brak zamówień oczekujących.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Użytkownik</th>
                    <th>Kwota</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= e($o['id']) ?></td>
                        <td><?= e($o['user_name'] ?? 'Guest') ?></td>
                        <td><?= number_format($o['total'] ?? 0, 2, ',', ' ') ?> zł</td>
                        <td><?= e($o['created_at']) ?></td>
                        <td><?= e($o['status'] ?? '') ?></td>
                        <td><a href="<?= url('employee/orders/' . $o['id']) ?>" class="btn">Pokaż</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

