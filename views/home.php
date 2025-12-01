<?php
?>
<div class="hero">
    <div class="container">
        <h1>Witamy w Electro Shop</h1>
        <p>Najlepsza elektronika w najlepszych cenach</p>
    </div>
</div>

<div class="container">
    <div class="filters">
        <form method="GET" action="<?= url() ?>" class="filter-form">
            <div class="filter-group">
                <label>Kategoria:</label>
                <select name="category" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category->id ?>" <?= (isset($_GET['category']) && $_GET['category'] == $category->id) ? 'selected' : '' ?>>
                            <?= e($category->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Marka:</label>
                <select name="brand" onchange="this.form.submit()">
                    <option value="">Wszystkie</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= e($brand) ?>" <?= (isset($_GET['brand']) && $_GET['brand'] == $brand) ? 'selected' : '' ?>>
                            <?= e($brand) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Cena od:</label>
                <input type="number" name="min_price" value="<?= e($_GET['min_price'] ?? '') ?>" placeholder="0" step="0.01">
            </div>

            <div class="filter-group">
                <label>Cena do:</label>
                <input type="number" name="max_price" value="<?= e($_GET['max_price'] ?? '') ?>" placeholder="9999" step="0.01">
            </div>

            <div class="filter-group">
                <label>Sortuj:</label>
                <select name="sort" onchange="this.form.submit()">
                    <option value="latest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'latest') ? 'selected' : '' ?>>Najnowsze</option>
                    <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Cena: od najniższej</option>
                    <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Cena: od najwyższej</option>
                    <option value="name" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : '' ?>>Nazwa A-Z</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Filtruj</button>
            <a href="<?= url() ?>" class="btn btn-secondary" style="margin-left:8px;">Wyczyść filtry</a>
         </form>
    </div>

    <div class="products-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php
                        // Numeric comparison: don't use isset() because Model uses __get
                        $old_raw = $product->old_price;
                        $price_raw = $product->price;
                        $oldp = ($old_raw !== null && $old_raw !== '') ? floatval($old_raw) : null;
                        $p = ($price_raw !== null && $price_raw !== '') ? floatval($price_raw) : null;
                        $cond = ($oldp !== null && $p !== null && $oldp > $p);
                    ?>
                     <a href="<?= url('product/' . $product->slug) ?>">
                         <div class="product-image">
                            <?php if ($cond): ?>
                                <span class="badge-discount">-<?= e($product->getDiscountPercentage()) ?>%</span>
                            <?php endif; ?>
                         <?php if ($product->image): ?>
                                 <?php $pimg = $product->image; ?>
                                 <?php if (filter_var($pimg, FILTER_VALIDATE_URL)): ?>
                                     <img src="<?= $pimg ?>" alt="<?= e($product->name) ?>">
                                 <?php else: ?>
                                     <img src="<?= asset('storage/' . $pimg) ?>" alt="<?= e($product->name) ?>">
                                 <?php endif; ?>
                             <?php else: ?>
                                 <img src="https://via.placeholder.com/300x300?text=No+Image" alt="<?= e($product->name) ?>">
                             <?php endif; ?>
                         </div>
                         <div class="product-info">
                             <h3><?= e($product->name) ?></h3>
                             <?php $category = $product->getCategory(); ?>
                             <?php if ($category): ?>
                                 <p class="product-category"><?= e($category->name) ?></p>
                             <?php endif; ?>
                             <div class="product-price">
                                 <?php if ($product->old_price): ?>
                                     <span class="old-price"><?= number_format($product->old_price, 2, ',', ' ') ?> zł</span>
                                 <?php endif; ?>
                                 <span class="current-price"><?= number_format($product->price, 2, ',', ' ') ?> zł</span>
                             </div>
                         </div>
                     </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <p>Nie znaleziono produktów spełniających kryteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
