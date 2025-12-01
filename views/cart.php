<div class="container cart-page">
    <h1>Koszyk</h1>
    <?php if (empty($cartItems)): ?>
        <p>Twój koszyk jest pusty.</p>
        <a href="<?= url() ?>" class="btn">Kontynuuj zakupy</a>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Ilość</th>
                    <th>Cena</th>
                    <th>Razem</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item):
                    $product = $item->getProduct();
                ?>
                <tr>
                    <td>
                        <a href="<?= url('product/' . ($product->slug ?? $product->id)) ?>"><?= e($product->name ?? 'Produkt') ?></a>
                    </td>
                    <td>
                        <form action="<?= url('cart/update/' . $item->id) ?>" method="POST" style="display:inline-block;">
                            <?= csrf_field() ?>
                            <input type="number" name="quantity" value="<?= intval($item->quantity) ?>" min="1" max="<?= $product->stock ?? 1 ?>">
                            <button class="btn">Zmień</button>
                        </form>
                    </td>
                    <td><?= number_format($product->price ?? 0, 2, ',', ' ') ?> zł</td>
                    <td><?= number_format(($product->price ?? 0) * $item->quantity, 2, ',', ' ') ?> zł</td>
                    <td>
                        <form action="<?= url('cart/remove/' . $item->id) ?>" method="POST">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger">Usuń</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cart-summary">
            <strong>Razem: <?= number_format($total, 2, ',', ' ') ?> zł</strong>
            <a href="<?= url('checkout') ?>" class="btn btn-primary">Przejdź do płatności</a>
        </div>
    <?php endif; ?>
</div>
