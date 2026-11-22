<?php

namespace src\controllers;

use src\models\Articles;
use src\exceptions\NotFoundException;
use src\exceptions\UnauthorizedException;
use src\exceptions\ForbiddenException;
use src\exceptions\InvalidArgumentException;

class ArticlesController extends Controller
{
    public function index()
    {
        $articles = Articles::findAll();
        $this->view->renderHtml('articles/index.php', ['articles' => $articles]);
    }

    public function view($id)
    {
        $article = Articles::getById($id);
        if ($article === null) {
            throw new NotFoundException();
        }
        $this->view->renderHtml('articles/view.php', ['article' => $article]);
    }

    public function add()
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $article = Articles::create($_POST, $_FILES['img'] ?? null, $this->user);
                header('Location: ' . url('articles/article/' . $article->getId()));
                exit;
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/add.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('articles/add.php');
    }

    public function edit($id)
    {
        $article = $this->getEditableArticle($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $article->updateFromArray($_POST, $_FILES['img'] ?? null);
                header('Location: ' . url('articles/article/' . $article->getId()));
                exit;
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/edit.php', ['article' => $article, 'error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('articles/edit.php', ['article' => $article]);
    }

    public function delete($id)
    {
        $article = $this->getEditableArticle($id);
        // Удаляем только после нажатия кнопки (POST), а не по ссылке
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $article->delete();
            header('Location: ' . url('articles/'));
        } else {
            header('Location: ' . url('articles/article/' . $article->getId()));
        }
        exit;
    }

    public function search()
    {
        $q = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
        $articles = $q !== '' ? Articles::search($q) : null;
        $this->view->renderHtml('articles/search.php', ['q' => $q, 'articles' => $articles]);
    }

    // Находит статью и проверяет, что текущий пользователь может её менять
    private function getEditableArticle($id): Articles
    {
        $article = Articles::getById($id);
        if ($article === null) {
            throw new NotFoundException();
        }
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        if (!$article->isEditableBy($this->user)) {
            throw new ForbiddenException();
        }
        return $article;
    }
}
