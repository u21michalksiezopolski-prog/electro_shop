<div class="container auth-page">
    <h1>Resetowanie hasła</h1>
    <p>Wpisz swój adres e-mail — wyślemy link do zresetowania hasła.</p>
    <form action="<?= url('forgot-password') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <button class="btn btn-primary" type="submit">Wyślij link resetujący</button>
    </form>
    <p><a href="<?= url('login') ?>">Powrót do logowania</a></p>
</div>

