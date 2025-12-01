<div class="container profile-page">
    <h1>Profil</h1>
    <p><a href="<?= url('profile/orders') ?>" class="btn btn-secondary" style="margin-bottom:12px; display:inline-block;">Historia zamówień</a></p>
    <form action="<?= url('profile') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Imię i nazwisko</label>
            <input type="text" name="name" value="<?= e($user->name ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= e($user->email ?? '') ?>" disabled>
        </div>
        <div class="form-group">
            <label>Telefon</label>
            <input type="text" name="phone" value="<?= e($user->phone ?? '') ?>">
        </div>
        <button class="btn btn-primary" type="submit">Zapisz</button>
    </form>

    <hr>

    <h2>Zmiana hasła</h2>
    <form action="<?= url('profile') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Aktualne hasło</label>
            <input type="password" name="current_password">
        </div>
        <div class="form-group">
            <label>Nowe hasło</label>
            <input type="password" name="new_password">
        </div>
        <div class="form-group">
            <label>Powtórz nowe hasło</label>
            <input type="password" name="new_password_confirmation">
        </div>
        <button class="btn btn-secondary" type="submit">Zmień hasło</button>
    </form>
</div>
