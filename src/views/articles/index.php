<div class="container">
    <div class="title-row">
        <h1>Статьи</h1>
        <?php if ($user): ?>
            <a class="btn" href="articles/add">+ Добавить статью</a>
        <?php endif; ?>
    </div>

    <?php if (empty($articles)): ?>
        <p class="muted">Статей пока нет.</p>
    <?php endif; ?>

    <div class="articles-list">
        <?php foreach ($articles as $article): ?>
            <?php include __DIR__ . '/_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
