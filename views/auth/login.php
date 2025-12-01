<div class="container auth-page">
    <h1>Logowanie</h1>
    <form action="<?= url('login') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Hasło</label>
            <input type="password" name="password" required>
        </div>
        <button class="btn btn-primary" type="submit">Zaloguj</button>
    </form>
    <p><a href="<?= url('forgot-password') ?>">Nie pamiętasz hasła?</a></p>
    <p>Nie masz konta? <a href="<?= url('register') ?>">Zarejestruj się</a></p>
</div>

