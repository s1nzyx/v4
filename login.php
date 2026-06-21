<?php
// Подключение конфигурации и проверка сессии
require_once 'config.php';

// Если пользователь уже авторизован, перенаправляем его в профиль, чтобы не показывать форму входа
if (isset($_SESSION['user'])) {
    header('Location: profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>COMFY. — Авторизация</title>
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
            <a href="registration.php" class="btn-dark">Регистрация</a>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <div class="auth-banner-side">
                <div class="banner-watermark" style="font-size: 32px; color: rgba(0,0,0,0.07);">ДЭМОЭКЗАМЕН место для баннера</div>
            </div>

            <div class="auth-form-side">
                <h2>Авторизация</h2>

                <?php if (isset($_SESSION['auth_error'])): ?>
                    <p style="color: red; margin-bottom: 15px; font-size: 14px;"><?= $_SESSION['auth_error']; unset($_SESSION['auth_error']); ?></p>
                <?php endif; ?>

                <form action="controllers/action_login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn-dark" style="width: 100%; padding: 12px; margin-top: 10px;">Войти</button>
                </form>

                <div class="auth-switch-link">
                    Нет аккаунта? <a href="registration.php">Зарегистрироваться</a>
                </div>
            </div>
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