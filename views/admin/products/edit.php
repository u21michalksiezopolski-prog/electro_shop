<div class="container admin-products-edit">
    <h1>Edytuj produkt</h1>
    <form action="<?= url('admin/products/' . $product->id) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Nazwa</label>
            <input type="text" name="name" value="<?= e($product->name) ?>" required>
        </div>
        <div class="form-group">
            <label>Kategoria</label>
            <select name="category_id">
                <option value="0">Brak</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c->id ?>" <?= ($product->category_id == $c->id) ? 'selected' : '' ?>><?= e($c->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Cena</label>
            <input type="number" step="0.01" name="price" value="<?= e($product->price) ?>">
        </div>
        <div class="form-group">
            <label>Ilość</label>
            <input type="number" name="stock" value="<?= intval($product->stock) ?>">
        </div>
        <div class="form-group">
            <label>Obecny obraz</label>
            <?php if ($product->image): ?>
                <?php $img = $product->image; ?>
                <?php if (filter_var($img, FILTER_VALIDATE_URL)): ?>
                    <img src="<?= $img ?>" style="max-width:150px; display:block;">
                <?php else: ?>
                    <img src="<?= asset('storage/' . $img) ?>" style="max-width:150px; display:block;">
                <?php endif; ?>
            <?php else: ?>
                <p>Brak obrazka</p>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Zmień obraz (upload)</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <label>Obraz (URL)</label>
            <input type="text" name="image_url" placeholder="https://..." value="<?= e($product->image) ?>">
        </div>
        <button class="btn btn-primary" type="submit">Zapisz</button>
    </form>
</div>

