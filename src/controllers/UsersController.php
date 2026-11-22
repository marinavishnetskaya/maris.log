<?php

namespace src\controllers;

use src\models\Users;
use src\models\UsersAuthService;
use src\exceptions\InvalidArgumentException;

class UsersController extends Controller
{
    public function signUp()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                Users::signUp($_POST);
                $this->view->renderHtml('users/signUpSuccess.php');
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
            }
            return;
        }
        $this->view->renderHtml('users/signUp.php');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $user = Users::login($_POST);
                UsersAuthService::createToken($user);
                header('Location: ' . url('articles/'));
                exit;
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('users/login.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHtml('users/login.php');
    }

    public function logout()
    {
        UsersAuthService::deleteToken();
        header('Location: ' . url('articles/'));
        exit;
    }
}
