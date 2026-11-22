<?php

namespace src\models;

use src\exceptions\InvalidArgumentException;

class Users extends ActiveRecordEntity
{
    protected $nickname;
    protected $email;
    protected $is_confirmed;
    protected $password_hash;
    protected $created_at;
    protected $role;
    protected $authtoken;

    protected static function getTableName(): string
    {
        return 'users';
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function getAuthToken(): string
    {
        return $this->authtoken;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    private static function newToken(): string
    {
        return sha1(random_bytes(100)) . sha1(random_bytes(100));
    }

    public function refreshAuthToken(): void
    {
        $this->authtoken = self::newToken();
    }

    // Значение из формы как строка (на случай, если пришёл массив)
    private static function field(array $data, string $key): string
    {
        return isset($data[$key]) && is_string($data[$key]) ? trim($data[$key]) : '';
    }

    public static function signUp(array $userData): Users
    {
        $nickname = self::field($userData, 'nickname');
        $email = self::field($userData, 'email');
        // пароль не обрезаем: пробелы в нём тоже символы
        $password = isset($userData['password']) && is_string($userData['password']) ? $userData['password'] : '';

        if ($nickname === '') {
            throw new InvalidArgumentException('Не передан логин');
        }
        if ($email === '') {
            throw new InvalidArgumentException('Не передан email');
        }
        if ($password === '') {
            throw new InvalidArgumentException('Не передан пароль');
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $nickname)) {
            throw new InvalidArgumentException('В логине можно использовать только латиницу и цифры');
        }
        if (strlen($nickname) > 50) {
            throw new InvalidArgumentException('Логин не длиннее 50 символов');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Некорректный email');
        }
        if (mb_strlen($password) < 8) {
            throw new InvalidArgumentException('Пароль не короче 8 символов');
        }
        if (static::findOneByColumn('email', $email) !== null) {
            throw new InvalidArgumentException('Пользователь с таким email уже есть');
        }
        if (static::findOneByColumn('nickname', $nickname) !== null) {
            throw new InvalidArgumentException('Пользователь с таким логином уже есть');
        }

        $user = new Users();
        $user->nickname = $nickname;
        $user->email = $email;
        $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
        $user->role = 'user';
        $user->authtoken = self::newToken();
        $user->save();
        return $user;
    }

    public static function login(array $loginData): Users
    {
        $nickname = self::field($loginData, 'nickname');
        $password = isset($loginData['password']) && is_string($loginData['password']) ? $loginData['password'] : '';

        if ($nickname === '') {
            throw new InvalidArgumentException('Не передан логин');
        }
        if ($password === '') {
            throw new InvalidArgumentException('Не передан пароль');
        }

        $user = static::findOneByColumn('nickname', $nickname);
        if ($user === null || !password_verify($password, $user->getPasswordHash())) {
            throw new InvalidArgumentException('Неправильный логин или пароль');
        }

        $user->refreshAuthToken();
        $user->save();
        return $user;
    }
}
