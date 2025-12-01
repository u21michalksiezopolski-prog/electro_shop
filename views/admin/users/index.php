<?php
// views/admin/users/index.php
// Expects $users (array of User instances)
?>
<div class="container admin-users">
    <h1>Użytkownicy</h1>
    <a href="<?= url('admin/dashboard') ?>" class="btn">Powrót do panelu</a>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imię</th>
                <th>Email</th>
                <th>Rola</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= e($u->id) ?></td>
                    <td><?= e($u->name) ?></td>
                    <td><?= e($u->email) ?></td>
                    <td><?= e($u->role) ?></td>
                    <td>
                        <?php $current = auth(); ?>
                        <?php if ($current && $current->id == $u->id): ?>
                            <!-- Prevent changing own role from UI to avoid accidental lockout -->
                            <em>Nie można zmienić własnej roli.</em>
                        <?php else: ?>
                            <form action="<?= url('admin/users/' . $u->id . '/role') ?>" method="POST" style="display:inline-block; margin-right:8px;">
                                <?= csrf_field() ?>
                                <select name="role">
                                    <option value="customer" <?= $u->role === 'customer' ? 'selected' : '' ?>>Klient</option>
                                    <option value="employee" <?= $u->role === 'employee' ? 'selected' : '' ?>>Pracownik</option>
                                    <option value="admin" <?= $u->role === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button class="btn">Zmień</button>
                            </form>
                            <?php // Quick toggle: only for non-admin users, toggle between employee and customer ?>
                            <?php if ($u->role !== 'admin'): ?>
                                <form action="<?= url('admin/users/' . $u->id . '/role') ?>" method="POST" style="display:inline-block;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="role" value="<?= $u->role === 'employee' ? 'customer' : 'employee' ?>">
                                    <button class="btn"><?= $u->role === 'employee' ? 'Odbierz pracownika' : 'Nadaj pracownika' ?></button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
