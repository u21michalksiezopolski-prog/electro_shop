<div class="container favorites-page">
    <h1>Ulubione</h1>
    <?php if (empty($favorites)): ?>
        <p>Brak ulubionych produktów.</p>
        <a href="<?= url() ?>" class="btn">Przejdź do sklepu</a>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($favorites as $fav): $p = $fav->getProduct(); ?>
                <div class="product-card">
                    <a href="<?= url('product/' . ($p->slug ?? $p->id)) ?>">
                        <?php if (!empty($p->image)): ?>
                            <img src="<?= asset('storage/' . $p->image) ?>" alt="<?= e($p->name ?? '') ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x300?text=No+Image" alt="<?= e($p->name ?? '') ?>">
                        <?php endif; ?>
                        <h4><?= e($p->name ?? '') ?></h4>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
