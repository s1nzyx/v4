<?php
require_once 'config.php';

$basket_products = [];
$total_price = 0;

if (!empty($_SESSION['basket'])) {
    // Получаем ID всех товаров из корзины для безопасного запроса
    $ids = array_keys($_SESSION['basket']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $quantity = $_SESSION['basket'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total_price += $subtotal;
        
        $basket_products[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

// Обработка очистки корзины
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'clear') {
        $_SESSION['basket'] = [];
        header('Location: basket.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>COMFY. — Корзина</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .basket-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .basket-table th, .basket-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .basket-table th {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .basket-img-placeholder {
            width: 60px;
            height: 60px;
            background-color: #FFF9E3;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
        }
        .basket-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
        }
    </style>
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
                <span class="user-email"><?= htmlspecialchars($_SESSION['user']['email']); ?></span>
                <a href="profile.php" class="btn-dark">Профиль</a>
                <a href="logout.php" style="color: #000; font-size: 14px; text-decoration: none;">Выйти</a>
            <?php else: ?>
                <a href="login.php" class="btn-dark">Войти</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h2 class="section-title">Корзина выбранных товаров</h2>

        <?php if (empty($basket_products)): ?>
            <div style="text-align: center; padding: 50px 0;">
                <p style="color: #666; margin-bottom: 20px;">Ваша корзина пуста</p>
                <a href="product_list.php" class="btn-dark">Перейти к покупкам</a>
            </div>
        <?php else: ?>
            <table class="basket-table">
                <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Название товара</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($basket_products as $item): ?>
                        <tr>
                            <td>
                                <div class="basket-img-placeholder">ФОТО</div>
                            </td>
                            <td><strong><?= htmlspecialchars($item['name']); ?></strong></td>
                            <td><?= htmlspecialchars($item['price']); ?>.тг</td>
                            <td><?= htmlspecialchars($item['quantity']); ?> шт.</td>
                            <td><strong><?= htmlspecialchars($item['subtotal']); ?>.тг</strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="basket-summary">
                <div>
                    <a href="basket.php?action=clear" style="color: red; text-decoration: none; font-size: 14px;">Очистить корзину</a>
                </div>
                <div style="display: flex; align-items: center; gap: 30px;">
                    <div class="total-amount">Итого: <?= htmlspecialchars($total_price); ?>.тг</div>
                    <a href="order.php" class="btn-dark" style="padding: 12px 25px;">Оформить заказ</a>
                </div>
            </div>
        <?php endif; ?>
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