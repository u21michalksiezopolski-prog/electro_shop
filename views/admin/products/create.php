<div class="container admin-products-create">
    <h1>Dodaj produkt</h1>
    <a href="<?= url('admin/dashboard') ?>" class="btn">Powrót do panelu</a>
    <form action="<?= url('admin/products') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Nazwa</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Kategoria</label>
            <select name="category_id">
                <option value="0">Brak</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>"><?= e($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Cena</label>
            <input type="number" step="0.01" name="price">
        </div>
        <div class="form-group">
            <label>Ilość</label>
            <input type="number" name="stock" value="0">
        </div>
        <div class="form-group">
            <label>Obraz (upload)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <label>Obraz (URL)</label>
            <input type="text" name="image_url" placeholder="https://...">
        </div>
        <button class="btn btn-primary" type="submit">Utwórz</button>
    </form>
</div>
