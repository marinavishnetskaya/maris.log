<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= e(BASE_URL) ?>">
    <title>mari$ bo$$</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container header-inner">
            <a class="logo" href="./">mari$ bo$$</a>
            <nav class="nav">
                <a href="articles/">Статьи</a>
                <a href="articles/search">Поиск</a>
                <?php if ($user): ?>
                    <a href="articles/add">Новая статья</a>
                    <span class="nav-user"><?= e($user->getNickname()) ?></span>
                    <a href="users/logout">Выход</a>
                <?php else: ?>
                    <a href="users/signUp">Регистрация</a>
                    <a href="users/login">Вход</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <div>
                <h3>Контакты</h3>
                <p>ТГ: @yourBlog</p>
                <p>Телефон: +7(999)104-86-41</p>
                <p>Email: HistoryBlog@gmail.com</p>
            </div>
            <div>
                <h3>Все права защищены</h3>
                <p>©2026 mari$ bo$$</p>
            </div>
        </div>
    </footer>
</body>
</html>
