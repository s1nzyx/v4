<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_repeat = $_POST['password_repeat'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['auth_error'] = "Все поля обязательны для заполнения.";
        header('Location: ../registration.php');
        exit;
    }

    if ($password !== $password_repeat) {
        $_SESSION['auth_error'] = "Пароли не совпадают.";
        header('Location: ../registration.php');
        exit;
    }

    // Проверяем, существует ли пользователь
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['auth_error'] = "Пользователь с таким Email уже зарегистрирован.";
        header('Location: ../registration.php');
        exit;
    }

    // Хэшируем пароль для безопасности (критерий бэкенда)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Сохраняем в БД
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $email, $hashedPassword])) {
        // Автоматический вход после регистрации
        $_SESSION['user'] = [
            'id' => $pdo->lastInsertId(),
            'name' => $name,
            'email' => $email
        ];
        header('Location: ../profile.php');
        exit;
    } else {
        $_SESSION['auth_error'] = "Произошла ошибка при регистрации.";
        header('Location: ../registration.php');
        exit;
    }
}