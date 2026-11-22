<div class="container">
    <form class="form" action="users/login" method="POST">
        <h1>Вход</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <label for="nickname">Логин</label>
        <input type="text" id="nickname" name="nickname" value="<?= e($_POST['nickname'] ?? '') ?>" required>

        <label for="password">Пароль</label>
        <input type="password" id="password" name="password" required>

        <button class="btn" type="submit">Войти</button>
        <p class="muted">Нет аккаунта? <a href="users/signUp">Регистрация</a></p>
    </form>
</div>
