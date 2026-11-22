<div class="container">
    <form class="form form-wide" action="articles/article/<?= $article->getId() ?>/edit" method="POST" enctype="multipart/form-data">
        <h1>Редактирование статьи</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= e($error) ?></p>
        <?php endif; ?>

        <label for="name">Название</label>
        <input type="text" id="name" name="name" value="<?= e($_POST['name'] ?? $article->getName()) ?>" required>

        <label for="text">Текст</label>
        <textarea id="text" name="text" rows="10" required><?= e($_POST['text'] ?? $article->getText()) ?></textarea>

        <?php if ($article->getImg()): ?>
            <p class="muted">Сейчас:</p>
            <img class="edit-img" src="<?= e($article->getImg()) ?>" alt="">
        <?php endif; ?>

        <label for="img">Новая картинка (необязательно)</label>
        <input type="file" id="img" name="img" accept="image/*">

        <div class="actions">
            <button class="btn" type="submit">Сохранить</button>
            <a href="articles/article/<?= $article->getId() ?>">Отмена</a>
        </div>
    </form>
</div>
