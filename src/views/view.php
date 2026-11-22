<?php

namespace src\views;

class View
{
    private $layout;
    private $extraVars = [];

    public function __construct(string $layout)
    {
        $this->layout = $layout;
    }

    // Переменные, которые будут доступны во всех шаблонах (например, $user)
    public function setVar(string $name, $value): void
    {
        $this->extraVars[$name] = $value;
    }

    public function renderHtml(string $viewName, array $vars = [], int $code = 200): void
    {
        http_response_code($code);
        $content = $this->renderFile($viewName, $vars);
        echo $this->renderFile('layouts/' . $this->layout . '.php', ['content' => $content]);
    }

    public function renderFile(string $fileName, array $vars): string
    {
        extract($vars);
        extract($this->extraVars);
        $path = __DIR__ . '/' . $fileName;
        if (!file_exists($path)) {
            return 'Не найден шаблон: ' . htmlspecialchars($fileName);
        }
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
