<div class="container">
    <h1>Поиск по сайту</h1>

    <form class="search-form" action="articles/search" method="GET">
        <input
            type="text"
            name="q"
            value="<?= e($q) ?>"
            placeholder="Введите запрос"
            required>
        <button class="btn" type="submit">Найти</button>
    </form>

    <?php if ($articles !== null): ?>
        <h2>Результаты по запросу: <?= e($q) ?></h2>
        <?php if (empty($articles)): ?>
            <p class="muted">Ничего не найдено.</p>
        <?php endif; ?>
        <div class="articles-list">
            <?php foreach ($articles as $article): ?>
                <?php include __DIR__ . '/_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

