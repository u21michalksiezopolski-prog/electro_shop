<?php
$user = auth();
?>
<div class="container checkout-page">
    <h1>Podsumowanie zamówienia</h1>

    <div class="checkout-grid">
        <div class="checkout-form">
            <form action="<?= url('orders') ?>" method="POST">
                <?= csrf_field() ?>

                <?php if (!$user): ?>
                    <p>Możesz złożyć zamówienie jako gość — prosimy o podanie danych do wysyłki i kontaktu.</p>
                <?php endif; ?>

                <div class="form-group">
                    <label>Adres email</label>
                    <input type="email" name="email" value="<?= e($user->email ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Imię i nazwisko</label>
                    <input type="text" name="name" value="<?= e($user->name ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Telefon</label>
                    <input type="text" name="phone" value="<?= e($user->phone ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Adres</label>
                    <input type="text" name="address" value="<?= e($user->address ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Miasto</label>
                    <input type="text" name="city" value="<?= e($user->city ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Kod pocztowy</label>
                    <input type="text" name="postal_code" value="<?= e($user->postal_code ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Metoda płatności</label>
                    <select name="payment_method">
                        <option value="card">Karta</option>
                        <option value="transfer">Przelew</option>
                        <option value="cash">Płatność przy odbiorze</option>
                    </select>
                </div>

                <button class="btn btn-primary" type="submit">Złóż zamówienie</button>
            </form>
        </div>

        <aside class="checkout-summary">
            <h3>Podsumowanie</h3>
            <?php if (empty($cartItems)): ?>
                <p>Brak pozycji w koszyku.</p>
            <?php else: ?>
                <ul class="checkout-items">
                    <?php foreach ($cartItems as $item): $product = $item->getProduct(); ?>
                        <li><?= e($product->name ?? 'Produkt') ?> x <?= intval($item->quantity) ?> — <?= number_format($item->getTotal(), 2, ',', ' ') ?> zł</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="checkout-totals">
                <p>Subtotal: <?= number_format($subtotal ?? 0, 2, ',', ' ') ?> zł</p>
                <p>Podatek (23%): <?= number_format($tax ?? 0, 2, ',', ' ') ?> zł</p>
                <p>Koszt dostawy: <?= number_format($shipping ?? 0, 2, ',', ' ') ?> zł</p>
                <hr>
                <p><strong>Razem: <?= number_format($total ?? 0, 2, ',', ' ') ?> zł</strong></p>
            </div>
        </aside>
    </div>
</div>
