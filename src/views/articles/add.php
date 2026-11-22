<div class="container">
    <form class="form form-wide" action="articles/add" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <h1>Новая статья</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <label for="name">Название</label>
        <input type="text" id="name" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>

        <label for="text">Текст</label>
        <textarea id="text" name="text" rows="10" required><?= e($_POST['text'] ?? '') ?></textarea>

        <label for="img">Картинка (необязательно)</label>
        <input type="file" id="img" name="img" accept="image/*">

        <div class="actions">
            <button class="btn" type="submit">Создать статью</button>
            <a href="articles/">Назад к списку</a>
        </div>
    </form>
</div>

