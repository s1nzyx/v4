<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id'] ?? 0);

    if ($product_id > 0) {
        // Проверяем, существует ли такой товар в базе данных
        $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            // Если товар уже в корзине — увеличиваем количество, если нет — добавляем 1 шт.
            if (isset($_SESSION['basket'][$product_id])) {
                $_SESSION['basket'][$product_id]++;
            } else {
                $_SESSION['basket'][$product_id] = 1;
            }
        }
    }
}

// Возвращаем пользователя обратно на ту страницу, где он находился
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../product_list.php'));
exit;