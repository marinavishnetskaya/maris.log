<div class="container">
    <form class="form" action="users/signUp" method="POST">
        <h1>Регистрация</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <label for="nickname">Логин</label>
        <input type="text" id="nickname" name="nickname" value="<?= e($_POST['nickname'] ?? '') ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>

        <label for="password">Пароль (не короче 8 символов)</label>
        <input type="password" id="password" name="password" required>

        <button class="btn" type="submit">Зарегистрироваться</button>
        <p class="muted">Уже есть аккаунт? <a href="users/login">Вход</a></p>
    </form>
</div>
