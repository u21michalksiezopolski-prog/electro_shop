<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' - ' : '' ?>Electro Shop</title>
    <?php
    $css_path = full_url('css/style.css');
    $js_path = full_url('js/app.js');
    ?>
    <link rel="stylesheet" href="<?= $css_path ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?= url() ?>">
                    <i class="fas fa-bolt"></i>
                    <span>Electro Shop</span>
                </a>
            </div>
            <div class="nav-menu">
                <a href="<?= url('cart') ?>" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    Koszyk
                    <?php
                    $cartCount = 0;
                    if (auth_check()) {
                        $cartCount = \DB::selectOne("SELECT COUNT(*) as count FROM cart WHERE user_id = ?", [$_SESSION['user_id']])['count'] ?? 0;
                    } else {
                        $cartCount = \DB::selectOne("SELECT COUNT(*) as count FROM cart WHERE session_id = ?", [session_id()])['count'] ?? 0;
                    }
                    if ($cartCount > 0): ?>
                        <span class="cart-count"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
                <?php if (auth_check()): 
                    $user = auth();
                ?>
                    <a href="<?= url('profile') ?>" class="nav-link">
                        <i class="fas fa-user"></i>
                        <?= e($user->name) ?>
                    </a>
                    <?php if ($user->isAdmin()): ?>
                        <a href="<?= url('admin/dashboard') ?>" class="nav-link">
                            <i class="fas fa-cog"></i>
                            Panel Admin
                        </a>
                    <?php elseif ($user->isEmployee()): ?>
                        <a href="<?= url('employee/dashboard') ?>" class="nav-link">
                            <i class="fas fa-briefcase"></i>
                            Panel Pracownika
                        </a>
                    <?php endif; ?>
                    <form action="<?= url('logout') ?>" method="POST" style="display: inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="nav-link btn-logout">Wyloguj</button>
                    </form>
                <?php else: ?>
                    <a href="<?= url('login') ?>" class="nav-link">Logowanie</a>
                    <a href="<?= url('register') ?>" class="nav-link btn-primary">Rejestracja</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?= e($message) ?></div>
    <?php endif; ?>

    <?php if ($message = flash('error')): ?>
        <div class="alert alert-error"><?= e($message) ?></div>
    <?php endif; ?>

    <main>
        <?php 
        if (isset($content) && file_exists($content)) {
            include $content;
        } else {
            include __DIR__ . '/404.php';
        }
        ?>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Electro Shop</h3>
                    <p>Twój sklep z elektroniką</p>
                </div>
                <div class="footer-section">
                    <h4>Kontakt</h4>
                    <p>Email: info@electroshop.pl</p>
                    <p>Telefon: +48 123 456 789</p>
                </div>
                <div class="footer-section">
                    <h4>Informacje</h4>
                    <a href="#">Regulamin</a>
                    <a href="#">Polityka prywatności</a>
                    <a href="#">Zwroty</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Electro Shop. Wszelkie prawa zastrzeżone.</p>
            </div>
        </div>
    </footer>

    <script src="<?= $js_path ?>"></script>
</body>
</html>
