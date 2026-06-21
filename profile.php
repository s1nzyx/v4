<?php
require_once 'config.php';

// Если пользователь не авторизован — перенаправляем на страницу логина
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

// Загружаем историю заказов пользователя из БД
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>COMFY. — Личный кабинет</title>
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .profile-card {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 40px;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        .orders-table th, .orders-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        .orders-table th {
            background-color: #f5f5f5;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-open {
            background-color: #D4EFDF;
            color: #196F3D;
        }
        .status-closed {
            background-color: #E5E7E9;
            color: #5D6D7E;
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
            <span class="user-email"><?= htmlspecialchars($user['email']) ?></span>
            <a href="logout.php" style="color: #000; font-size: 14px; text-decoration: none;">Выйти</a>
        </div>
    </header>

    <main>
        <div class="profile-header">
            <h2 class="section-title" style="text-align: left; margin-bottom: 10px;">Личный кабинет</h2>
        </div>

        <div class="profile-card">
            <h3 style="margin-bottom: 10px;">Данные профиля</h3>
            <p style="margin-bottom: 5px;"><strong>ФИО:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>

        <h3 style="margin-bottom: 20px;">История ваших заказов</h3>

        <?php if (empty($orders)): ?>
            <p style="color: #666;">Вы ещё не совершали покупок в нашем магазине.</p>
        <?php else: ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>№ Заказа</th>
                        <th>Дата заказа</th>
                        <th>Дата доставки</th>
                        <th>Сумма</th>
                        <th>Способ оплаты</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($order['order_number']) ?></strong></td>
                            <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                            <td><?= date('d.m.Y', strtotime($order['delivery_date'])) ?></td>
                            <td><?= $order['total_price'] ?>.тг</td>
                            <td><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td>
                                <?php if ($order['status'] === 'open'): ?>
                                    <span class="status-badge status-open">Открыт (В обработке)</span>
                                <?php else: ?>
                                    <span class="status-badge status-closed">Закрыт</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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