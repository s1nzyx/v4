<?php
require_once 'config.php';

if (empty($_SESSION['basket'])) {
    header('Location: basket.php');
    exit;
}

// Высчитываем итоговую сумму
$total_price = 0;
$ids = array_keys($_SESSION['basket']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$products = $stmt->fetchAll();

foreach ($products as $product) {
    $total_price += $product['price'] * $_SESSION['basket'][$product['id']];
}

$order_success = false;
$error_message = '';

// Обработка создания заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $phone = trim($_POST['customer_phone'] ?? '');
    $address = trim($_POST['customer_address'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? '');

    if (!empty($name) && !empty($email) && !empty($phone) && !empty($address) && !empty($payment_method)) {
        
        $order_number = 'ORD-' . strtoupper(uniqid());
        $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
        $created_at = date('Y-m-d');
        $delivery_date = date('Y-m-d', strtotime('+3 days')); // Автоматическая дата доставки (+3 дня)

        try {
            $pdo->beginTransaction();

            // 1. Вставляем запись в таблицу заказов
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, customer_name, customer_email, customer_phone, customer_address, payment_method, total_price, status, created_at, delivery_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'open', ?, ?)");
            $stmt->execute([$user_id, $order_number, $name, $email, $phone, $address, $payment_method, $total_price, $created_at, $delivery_date]);
            
            $order_id = $pdo->lastInsertId();

            // 2. Вставляем все позиции из корзины
            foreach ($products as $product) {
                $qty = $_SESSION['basket'][$product['id']];
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $product['id'], $product['price'], $qty]);
            }

            $pdo->commit();
            
            // Очищаем корзину после успешного заказа
            $_SESSION['basket'] = [];
            $order_success = true;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Ошибка при сохранении заказа: " . $e->getMessage();
        }
    } else {
        $error_message = "Пожалуйста, заполните все поля формы.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>COMFY. — Оформление заказа</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .order-container {
            display: flex;
            gap: 50px;
        }
        .order-form-block {
            flex: 2;
        }
        .order-summary-block {
            flex: 1;
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            height: fit-content;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 14px;
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
    </header>

    <main>
        <h2 class="section-title">Оформление заказа</h2>

        <?php if ($order_success): ?>
            <div style="text-align: center; padding: 40px; background-color: #E8F8F5; border-radius: 8px;">
                <h3 style="color: #27AE60; margin-bottom: 10px;">Заказ успешно оформлен!</h3>
                <p>Благодарим за покупку. Вы можете отслеживать статус в своём профиле.</p>
                <a href="index.php" class="btn-dark" style="margin-top: 20px;">На главную</a>
            </div>
        <?php else: ?>
            
            <?php if (!empty($error_message)): ?>
                <p style="color: red; margin-bottom: 20px;"><?= $error_message ?></p>
            <?php endif; ?>

            <div class="order-container">
                <div class="order-form-block">
                    <form action="order.php" method="POST">
                        <div class="form-group">
                            <label>ФИО получателя</label>
                            <input type="text" name="customer_name" class="form-control" value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['name']) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="customer_email" class="form-control" value="<?= isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['email']) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text" name="customer_phone" class="form-control" placeholder="+7 (707) 123-4567" required>
                        </div>
                        <div class="form-group">
                            <label>Адрес доставки</label>
                            <textarea name="customer_address" class="form-control" rows="3" placeholder="Город, улица, дом, квартира" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Метод оплаты</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="Онлайн-оплата картой">Онлайн-оплата картой</option>
                                <option value="Наличными при получении">Наличными при получении</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-dark" style="width: 100%; padding: 12px; margin-top: 15px;">Подтвердить заказ</button>
                    </form>
                </div>

                <div class="order-summary-block">
                    <h3 style="margin-bottom: 20px;">Ваш заказ</h3>
                    <?php foreach ($products as $product): ?>
                        <div class="summary-item">
                            <span><?= htmlspecialchars($product['name']) ?> (×<?= $_SESSION['basket'][$product['id']] ?>)</span>
                            <strong><?= $product['price'] * $_SESSION['basket'][$product['id']] ?>.тг</strong>
                        </div>
                    <?php endforeach; ?>
                    <hr style="border: none; border-top: 1px solid #ddd; margin: 15px 0;">
                    <div class="summary-item" style="font-size: 18px; font-weight: bold;">
                        <span>Итого:</span>
                        <span><?= $total_price ?>.тг</span>
                    </div>
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