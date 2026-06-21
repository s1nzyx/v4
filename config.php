<?php
// Проверка статуса сессии: если она еще не запущена, запускаем её
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Определение констант для подключения к базе данных
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'comfy_shop');

// Попытка подключения к базе данных через PDO
try {
    // Настройка соединения с использованием драйвера MySQL
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        // Включение режима выброса исключений при ошибках БД
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        // Установка режима выборки данных по умолчанию как ассоциативного массива
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Отключение эмуляции подготовленных запросов для повышения безопасности
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Остановка выполнения скрипта в случае неудачного подключения и вывод ошибки
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Инициализация корзины пользователя: если в сессии нет ключа 'basket', создаем пустой массив
if (!isset($_SESSION['basket'])) {
    $_SESSION['basket'] = [];
}
?>