<div class="container">
    <article class="article">
        <h1><?= e($article->getName()) ?></h1>
        <p class="muted">Автор: <?= e($article->getAuthorName()) ?> · <?= e($article->getDate()) ?></p>

        <?php if ($article->getImg()): ?>
            <img class="article-img" src="<?= e($article->getImg()) ?>" alt="<?= e($article->getName()) ?>">
        <?php endif; ?>

        <p class="article-text"><?= nl2br(e($article->getText())) ?></p>

        <?php if ($article->isEditableBy($user)): ?>
            <div class="actions">
                <a class="btn btn-light" href="articles/article/<?= $article->getId() ?>/edit">Изменить</a>
                <form action="articles/article/<?= $article->getId() ?>/delete" method="POST" onsubmit="return confirm('Удалить статью?')">
                    <button class="btn btn-danger" type="submit">Удалить</button>
                </form>
            </div>
        <?php endif; ?>

        <p><a href="articles/">← Назад к статьям</a></p>
    </article>
</div>
