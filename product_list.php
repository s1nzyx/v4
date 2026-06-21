<?php
require_once 'config.php';

// Получаем текущую категорию, если она выбрана
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$sort = $_GET['sort'] ?? 'default';

// Формируем базовый SQL-запрос
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id";
$params = [];

if ($category_id) {
    $query .= " WHERE p.category_id = ?";
    $params[] = $category_id;
}

// Добавляем сортировку согласно макету
if ($sort === 'price') {
    $query .= " ORDER BY p.price ASC";
} elseif ($sort === 'popularity') {
    $query .= " ORDER BY p.popularity DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Получаем имя текущей категории для заголовка
$page_title = "Все товары";
if ($category_id && !empty($products)) {
    $page_title = $products[0]['category_name'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>COMFY. — <?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>

    <header>
        <a href="index.php" class="logo">COMFY.</a>
        <nav>
            <a href="index.php">Главная</a>
            <a href="product_list.php">Категории</a>
            <a href="basket.php">Корзина</a>
        </nav>
        <div class="header-auth">
            <?php if (isset($_SESSION['user'])): ?>
                <span class="user-email"><?= htmlspecialchars($_SESSION['user']['email']) ?></span>
                <a href="profile.php" class="btn-dark">Профиль</a>
                <a href="logout.php" style="color: #000; font-size: 14px; text-decoration: none;">Выйти</a>
            <?php else: ?>
                <a href="login.php" class="btn-dark">Войти</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h2 class="section-title"><?= htmlspecialchars($page_title) ?></h2>

        <div class="filter-buttons">
            <a href="product_list.php?<?= $category_id ? "category_id=$category_id&" : "" ?>sort=popularity" class="btn-dark">По популярности</a>
            <a href="product_list.php?<?= $category_id ? "category_id=$category_id&" : "" ?>sort=price" class="btn-dark">По цене</a>
        </div>

        <div class="grid-container">
            <?php if (empty($products)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: #666;">В данной категории пока нет товаров.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="card">
                        <div class="card-image-placeholder">
                            ФОТО ТОВАРA
                        </div>
                        <div class="card-body">
                            <span class="card-title"><?= htmlspecialchars($product['name']) ?></span>
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
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="footer-socials">
            <a href="#">Facebook</a>
            <a href="#">Instagram</a>
            <a href="#">Pinterest</a>
            <a href="#">Telegram</a>
        </div>
        <div class="footer-phone">+1(111)111-11-11</div>
    </footer>

</body>
</html>