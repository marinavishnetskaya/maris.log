<section class="hero">
    <div class="container">
        <h1>Добро пожаловать в блог</h1>
        <p>Истории, мысли и идеи о жизни, технологиях и творчестве. Присоединяйтесь к нашему сообществу!</p>
    </div>
</section>

<div class="container">
    <h2>Последние статьи</h2>

    <?php if (empty($articles)): ?>
        <p class="muted">Статей пока нет.</p>
    <?php endif; ?>

    <div class="articles-list">
        <?php foreach ($articles as $article): ?>
            <?php include __DIR__ . '/../articles/_card.php'; ?>
        <?php endforeach; ?>
    </div>

    <p><a href="articles/">Все статьи →</a></p>
</div>
