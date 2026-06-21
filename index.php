<?php
require_once 'config.php';

// Получаем список товаров по одному представителю из каждой категории для Главной страницы
// (Имитируем Скриншот 1, где отображаются 6 базовых категорий/сувениров)
$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id GROUP BY p.category_id ASC LIMIT 6");
$featured_products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>COMFY. — Главная</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

    <header>
        <a href="index.php" class="logo">COMFY.</a>
        <nav>
            <a href="index.php">Главная</a>
            <a href="#description">Описание</a>
            <a href="product_list.php">Категории</a>
            <a href="basket.php">Корзина</a>
        </nav>
        <div class="header-auth">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="basket.php" class="btn-dark">Корзина</a>
                <span class="user-email"><?= htmlspecialchars($_SESSION['user']['email']) ?></span>
                <a href="logout.php" style="color: #000; font-size: 14px; margin-left: 10px; text-decoration: none;">Выйти</a>
            <?php else: ?>
                <a href="login.php" class="btn-dark">Войти</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="main-banner" id="description">
            <div class="banner-watermark">ДЭМОЭКЗАМЕН место для баннера</div>
            <h2>Краткая информация</h2>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam harum maxime impedit fugiat quam repellat animi expedita, itaque dignissimos culpa, quia minima voluptate quibusdam earum laborum voluptas dicta omnis placeat!</p>
        </div>

        <h2 class="section-title">Категории товаров</h2>
        
        <div class="grid-container">
    <?php foreach ($featured_products as $index => $product): ?>
        <div class="card">
            <div class="card-image-placeholder">
            ФОТО ТОВАРA
            </div>

            <div class="card-body">
                <a href="product_list.php?category_id=<?= $product['category_id'] ?>" class="card-title">
                    <?= htmlspecialchars($product['category_name']) ?>
                </a>
                <div class="card-footer">
                    <span class="card-price"><?= intval($product['price']) ?>.тг</span>
                    <form action="controllers/action_add_product.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="btn-dark">В корзину</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
    </main>

    <footer>
        <div class="footer-socials">
            <a href="#">Facebook</a>
            <a href="#">Instagram</a>
            <a href="#">Pinterest</a>
            <a href="#">Telegram</a>
        </div>
        <div class="footer-phone">
            +1(111)111-11-11
        </div>
    </footer>

</body>
</html>