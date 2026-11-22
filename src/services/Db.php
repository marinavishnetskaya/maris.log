<?php

namespace src\services;

use src\exceptions\DbException;

class Db
{
    private $pdo;
    private static $instance;

    private function __construct()
    {
        $db = (require __DIR__ . '/../config/settings.php')['db'];
        try {
            $this->pdo = new \PDO(
                'mysql:host=' . $db['host'] . ';port=' . ($db['port'] ?? 3306) . ';dbname=' . $db['dbname'] . ';charset=utf8mb4',
                $db['user'],
                $db['password']
            );
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new DbException('Ошибка при подключении к базе данных: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Выполняет запрос. Для SELECT возвращает массив объектов $className, для остальных - пустой массив.
    public function query(string $sql, array $params = [], string $className = 'stdClass'): array
    {
        try {
            $sth = $this->pdo->prepare($sql);
            $sth->execute($params);
            if ($sth->columnCount() === 0) {
                return [];
            }
            return $sth->fetchAll(\PDO::FETCH_CLASS, $className);
        } catch (\PDOException $e) {
            throw new DbException('Ошибка запроса к базе данных: ' . $e->getMessage());
        }
    }

    public function getLastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}
