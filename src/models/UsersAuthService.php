<?php

namespace src\models;

class UsersAuthService
{
    // Кладём в cookie "id:токен", пока браузер не закроют
    public static function createToken(Users $user): void
    {
        $token = $user->getId() . ':' . $user->getAuthToken();
        setcookie('token', $token, 0, BASE_URL, '', false, true);
    }

    public static function deleteToken(): void
    {
        setcookie('token', '', time() - 3600, BASE_URL, '', false, true);
    }

    public static function getUserByToken(): ?Users
    {
        $token = $_COOKIE['token'] ?? '';
        if (!is_string($token) || strpos($token, ':') === false) {
            return null;
        }
        [$userId, $authToken] = explode(':', $token, 2);
        $user = Users::getById((int) $userId);
        if ($user === null || !hash_equals($user->getAuthToken(), $authToken)) {
            return null;
        }
        return $user;
    }
}
