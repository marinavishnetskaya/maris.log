<?php

use src\controllers\ArticlesController;
use src\controllers\MainController;
use src\controllers\UsersController;

return [
    '~^/?$~' => [MainController::class, 'main'],
    '~^articles/?$~' => [ArticlesController::class, 'index'],
    '~^articles/add/?$~' => [ArticlesController::class, 'add'],
    '~^articles/search/?$~' => [ArticlesController::class, 'search'],
    '~^articles/article/(\d+)/?$~' => [ArticlesController::class, 'view'],
    '~^articles/article/(\d+)/edit/?$~' => [ArticlesController::class, 'edit'],
    '~^articles/article/(\d+)/delete/?$~' => [ArticlesController::class, 'delete'],
    '~^users/signUp/?$~' => [UsersController::class, 'signUp'],
    '~^users/login/?$~' => [UsersController::class, 'login'],
    '~^users/logout/?$~' => [UsersController::class, 'logout'],
];
