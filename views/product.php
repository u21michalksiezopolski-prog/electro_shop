<div class="container product-detail">
    <div class="product-main">
        <div class="product-gallery">
            <?php
            $images = [];
            $rawImages = $product->images ?? '';
            $rawImageSingle = $product->image ?? '';

            if (!empty($rawImageSingle)) {
                $cleanSingle = trim($rawImageSingle);
                if ( (strlen($cleanSingle) > 1) && (($cleanSingle[0] === '"' && substr($cleanSingle, -1) === '"') || ($cleanSingle[0] === "'" && substr($cleanSingle, -1) === "'")) ) {
                    $cleanSingle = substr($cleanSingle, 1, -1);
                }
                $cleanSingle = stripslashes($cleanSingle);
            } else {
                $cleanSingle = '';
            }

            if (!empty($rawImages)) {
                $raw = $rawImages;
                $decoded = null;
                for ($i = 0; $i < 3; $i++) {
                    $tmp = json_decode($raw, true);
                    if (is_array($tmp)) { $decoded = $tmp; break; }
                    if (is_string($tmp) && $tmp !== $raw) {
                        $raw = $tmp;
                        continue;
                    }
                    $raw = stripslashes($raw);
                }
                if (is_array($decoded)) {
                    $images = $decoded;
                } else {
                    $parts = array_filter(array_map('trim', explode(',', $rawImages)));
                    if (!empty($parts)) $images = $parts;
                }
            }

            if (empty($images) && !empty($cleanSingle)) {
                $images = [$cleanSingle];
            }

            $mainImage = null;
            if (!empty($cleanSingle) && filter_var($cleanSingle, FILTER_VALIDATE_URL)) {
                $mainImage = $cleanSingle;
            } elseif (!empty($images) && !empty($images[0])) {
                $first = trim($images[0]);
                if ( (strlen($first) > 1) && (($first[0] === '"' && substr($first, -1) === '"') || ($first[0] === "'" && substr($first, -1) === "'")) ) {
                    $first = substr($first, 1, -1);
                }
                $first = stripslashes($first);
                $mainImage = $first;
            }

            function img_src($val) {
                if (filter_var($val, FILTER_VALIDATE_URL)) return $val;
                return asset('storage/' . $val);
            }
            ?>

            <div class="product-image">
                <?php if ($mainImage): ?>
                    <img id="main-product-image" src="<?= e(img_src($mainImage)) ?>" alt="<?= e($product->name) ?>">
                <?php else: ?>
                    <img id="main-product-image" src="https://via.placeholder.com/500x500?text=No+Image" alt="<?= e($product->name) ?>">
                <?php endif; ?>
            </div>

            <?php if (!empty($images) && count($images) > 1): ?>
                <div class="product-thumbs">
                    <?php foreach ($images as $img): ?>
                        <img class="thumb" src="<?= e(img_src($img)) ?>" data-full="<?= e(img_src($img)) ?>" alt="<?= e($product->name) ?>" style="max-width:80px; cursor:pointer; margin:4px;">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <h1><?= e($product->name) ?></h1>
            <p class="product-category"><?= e($product->getCategory()->name ?? '') ?></p>
            <div class="product-price">
                <?php if ($product->old_price): ?>
                    <span class="old-price"><?= number_format($product->old_price, 2, ',', ' ') ?> zł</span>
                <?php endif; ?>
                <span class="current-price"><?= number_format($product->price, 2, ',', ' ') ?> zł</span>
            </div>
            <p class="product-stock <?= $product->isInStock() ? 'in-stock' : 'out-of-stock' ?>">
                <?= $product->isInStock() ? 'Dostępny' : 'Niedostępny' ?>
            </p>
            <div class="product-description">
                <?= nl2br(e($product->description)) ?>
            </div>
            <?php if ($product->isInStock()): ?>
                <form action="<?= url('cart/add/' . $product->id) ?>" method="POST" class="add-to-cart-form">
                    <?= csrf_field() ?>
                    <label>Ilość:</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product->stock ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-cart-plus"></i> Dodaj do koszyka</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($relatedProducts)): ?>
        <div class="related-products">
            <h3>Podobne produkty</h3>
            <div class="products-grid">
                <?php foreach ($relatedProducts as $rp): ?>
                    <div class="product-card">
                         <a href="<?= url('product/' . $rp->slug) ?>">
                            <div class="product-image">
                                <?php
                                    $dbRow = \DB::selectOne('SELECT image, images FROM products WHERE id = ?', [$rp->id]);
                                    $rimg_final = null;
                                    if (!empty($dbRow['image'])) {
                                        $rimg_final = trim($dbRow['image'], "'\"");
                                    } elseif (!empty($dbRow['images'])) {
                                        $rawr = $dbRow['images'];
                                        $decodedr = json_decode($rawr, true);
                                        if (is_array($decodedr) && !empty($decodedr)) {
                                            $rimg_final = trim($decodedr[0], "'\"");
                                        } else {
                                            $parts = array_filter(array_map('trim', explode(',', stripslashes($rawr))));
                                            if (!empty($parts)) $rimg_final = trim($parts[0], "'\"");
                                        }
                                    }
                                    if (!empty($rimg_final)) {
                                        $src = img_src($rimg_final);
                                        echo '<img src="' . e($src) . '" alt="' . e($rp->name) . '">';
                                    } else {
                                        echo '<img src="https://via.placeholder.com/300x300?text=No+Image" alt="' . e($rp->name) . '">';
                                    }
                                ?>
                            </div>
                            <div class="product-info">
                                <h4><?= e($rp->name) ?></h4>
                                <div class="product-price">
                                    <span class="current-price"><?= number_format($rp->price, 2, ',', ' ') ?> zł</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var thumbs = document.querySelectorAll('.product-thumbs .thumb');
    var main = document.getElementById('main-product-image');
    if (!main) return;
    thumbs.forEach(function(t){
        t.addEventListener('click', function(){
            var full = t.getAttribute('data-full');
            if (full) main.src = full;
        });
    });
});
</script>
