<?php
// Базовый путь проекта определяется сам, поэтому сайт работает
// и в корне домена (http://saktaganova.log/),
// и в подпапке (http://localhost/saktaganova.log-main/).
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_PATH', $scriptDir);       // '' или '/saktaganova.log-main'
define('BASE_URL', $scriptDir . '/');  // '/' или '/saktaganova.log-main/'

// url('articles/') -> /saktaganova.log-main/articles/
function url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}

// Короткая запись htmlspecialchars для шаблонов
function e($text): string
{
    if (!is_scalar($text)) {
        return '';
    }
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

// Автозагрузка классов. Регистр папок и файлов не важен (Windows и Linux ведут себя одинаково).
spl_autoload_register(function (string $className) {
    $relative = str_replace('\\', '/', $className) . '.php';
    $file = __DIR__ . '/' . $relative;
    if (is_file($file)) {
        require_once $file;
        return;
    }
    $current = __DIR__;
    foreach (explode('/', $relative) as $part) {
        $found = null;
        foreach (scandir($current) ?: [] as $entry) {
            if (strcasecmp($entry, $part) === 0) {
                $found = $entry;
                break;
            }
        }
        if ($found === null) {
            return;
        }
        $current .= '/' . $found;
    }
    if (is_file($current)) {
        require_once $current;
    }
});

function renderError($controller, string $template, array $vars, int $code): void
{
    // Если контроллер не успел создаться (например, упала БД), берём чистый View
    $view = $controller->view ?? null;
    if ($view === null) {
        $view = new \src\views\View('default');
        $view->setVar('user', null);
    }
    $view->renderHtml($template, $vars, $code);
}

$controller = null;
try {
    // Маршрут: сначала из .htaccess (?route=...), иначе берём из адреса
    if (isset($_GET['route'])) {
        $route = $_GET['route'];
    } else {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $path = rawurldecode($path);
        if (BASE_PATH !== '' && strpos($path, BASE_PATH) === 0) {
            $path = substr($path, strlen(BASE_PATH));
        }
        $path = ltrim($path, '/');
        $route = ($path === 'index.php') ? '' : $path;
    }

    $routes = require __DIR__ . '/src/config/routes.php';
    $isRouteFound = false;
    foreach ($routes as $pattern => $controllerAndAction) {
        if (preg_match($pattern, $route, $matches)) {
            $isRouteFound = true;
            break;
        }
    }
    if (!$isRouteFound) {
        $controller = new \src\controllers\MainController();
        throw new \src\exceptions\NotFoundException('Страница не найдена');
    }

    $controllerName = $controllerAndAction[0];
    $actionName = $controllerAndAction[1];
    unset($matches[0]);
    $controller = new $controllerName();
    $controller->$actionName(...$matches);
} catch (\src\exceptions\DbException $e) {
    renderError($controller, 'errors/500.php', ['error' => $e->getMessage()], 500);
} catch (\src\exceptions\NotFoundException $e) {
    renderError($controller, 'errors/404.php', [], 404);
} catch (\src\exceptions\UnauthorizedException $e) {
    renderError($controller, 'errors/401.php', [], 401);
} catch (\src\exceptions\ForbiddenException $e) {
    renderError($controller, 'errors/403.php', [], 403);
}
