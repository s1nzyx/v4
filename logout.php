<?php
// Подключение файла конфигурации
require_once 'config.php';

// Удаление данных пользователя из сессии
unset($_SESSION['user']);

// Уничтожение всей сессии
session_destroy();

// Перенаправление пользователя на главную страницу после выхода
header('Location: index.php');
exit;
?>