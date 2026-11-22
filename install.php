<?php
// Установка базы данных без phpMyAdmin: откройте в браузере
//   http://localhost/saktaganova.log-main/install.php
// Страница создаст базу и таблицы из database.sql и покажет, если что-то не так.
// После успешной установки этот файл можно удалить.
header('Content-Type: text/html; charset=UTF-8');

$db = (require __DIR__ . '/src/config/settings.php')['db'];
$messages = [];
$ok = false;

try {
    // Подключаемся без выбора базы, потому что её ещё может не быть
    $pdo = new PDO(
        'mysql:host=' . $db['host'] . ';port=' . ($db['port'] ?? 3306) . ';charset=utf8mb4',
        $db['user'],
        $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $messages[] = 'Подключение к MySQL: успешно';

    // Убираем строки-комментарии и режем файл на запросы по ";" в конце строки
    $sql = preg_replace('/^\s*--.*$/m', '', file_get_contents(__DIR__ . '/database.sql'));
    $queries = array_filter(array_map('trim', preg_split('/;\s*[\r\n]+/', $sql)));
    foreach ($queries as $query) {
        $pdo->exec($query);
    }
    $messages[] = 'База «' . $db['dbname'] . '», таблицы и тестовые данные созданы (запросов выполнено: ' . count($queries) . ')';

    $count = $pdo->query('SELECT COUNT(*) FROM `' . $db['dbname'] . '`.`articles`')->fetchColumn();
    $messages[] = 'Статей в базе: ' . $count;
    $ok = true;
} catch (PDOException $e) {
    $messages[] = 'ОШИБКА: ' . $e->getMessage();
    $messages[] = 'Проверьте: запущен ли модуль MySQL в OSPanel, верны ли host/port/пароль в src/config/settings.php';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Установка mari$ bo$$</title>
    <style>body { font-family: Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; }</style>
</head>
<body>
    <h1>Установка mari$ bo$$</h1>
    <?php foreach ($messages as $message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endforeach; ?>
    <?php if ($ok): ?>
        <p><strong>Готово.</strong> <a href="./">Открыть сайт</a> (вход: admin / 12345678)</p>
    <?php endif; ?>
</body>
</html>
