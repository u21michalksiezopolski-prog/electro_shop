<?php
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
?>
<div class="container auth-page">
    <h1>Ustaw nowe hasło</h1>
    <form action="<?= url('reset-password') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="email" value="<?= e($email) ?>">
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <div class="form-group">
            <label>Nowe hasło</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Powtórz nowe hasło</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button class="btn btn-primary" type="submit">Ustaw nowe hasło</button>
    </form>
    <p><a href="<?= url('login') ?>">Powrót do logowania</a></p>
</div>

