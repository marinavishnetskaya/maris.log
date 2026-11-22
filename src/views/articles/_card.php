<article class="card">
    <?php if ($article->getImg()): ?>
        <img class="card-img" src="<?= e($article->getImg()) ?>" alt="<?= e($article->getName()) ?>">
    <?php endif; ?>
    <div class="card-body">
        <h3><?= e($article->getName()) ?></h3>
        <p class="muted">Автор: <?= e($article->getAuthorName()) ?> · <?= e($article->getDate()) ?></p>
        <p><?= e($article->getExcerpt()) ?></p>
        <a class="btn" href="articles/article/<?= $article->getId() ?>">Подробнее</a>
    </div>
</article>
