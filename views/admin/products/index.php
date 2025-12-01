<div class="container admin-products-index">
    <h1>Produkty</h1>
    <a href="<?= url('admin/dashboard') ?>" class="btn">Powrót do panelu</a>
    <a href="<?= url('admin/products/create') ?>" class="btn btn-primary">Dodaj produkt</a>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nazwa</th>
                <th>Obraz</th>
                <th>Cena</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p->id ?></td>
                    <td><?= e($p->name) ?></td>
                    <td>
                        <?php if ($p->image): $img = $p->image; ?>
                            <?php if (filter_var($img, FILTER_VALIDATE_URL)): ?>
                                <img src="<?= $img ?>" style="max-width:80px;">
                            <?php else: ?>
                                <img src="<?= asset('storage/' . $img) ?>" style="max-width:80px;">
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($p->price, 2, ',', ' ') ?> zł</td>
                    <td>
                        <a href="<?= url('admin/products/' . $p->id . '/edit') ?>" class="btn">Edytuj</a>
                        <form action="<?= url('admin/products/' . $p->id . '/delete') ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger">Usuń</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
