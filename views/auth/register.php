<div class="container auth-page">
    <h1>Rejestracja</h1>
    <form action="<?= url('register') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Imię i nazwisko</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Telefon</label>
            <input type="text" name="phone">
        </div>
        <div class="form-group">
            <label>Hasło</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Powtórz hasło</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button class="btn btn-primary" type="submit">Zarejestruj się</button>
    </form>
</div>

