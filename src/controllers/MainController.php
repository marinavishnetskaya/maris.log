<?php

namespace src\controllers;

use src\models\Articles;

class MainController extends Controller
{
    public function main()
    {
        $articles = Articles::findLatest(3);
        $this->view->renderHtml('main/main.php', ['articles' => $articles]);
    }
}
