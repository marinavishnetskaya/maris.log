<?php

namespace src\models;

use src\services\Db;

abstract class ActiveRecordEntity
{
    protected $id;

    abstract protected static function getTableName(): string;

    public function getId(): int
    {
        return (int) $this->id;
    }

    public static function findAll(): array
    {
        return Db::getInstance()->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);
    }

    public static function getById($id): ?self
    {
        $result = Db::getInstance()->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE `id` = :id;',
            [':id' => $id],
            static::class
        );
        return $result[0] ?? null;
    }

    public static function findOneByColumn(string $columnName, $value): ?self
    {
        $result = Db::getInstance()->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE `' . $columnName . '` = :value LIMIT 1;',
            [':value' => $value],
            static::class
        );
        return $result[0] ?? null;
    }

    // Все свойства объекта в виде массива [имя => значение]
    private function getProperties(): array
    {
        $reflector = new \ReflectionObject($this);
        $result = [];
        foreach ($reflector->getProperties() as $property) {
            $name = $property->getName();
            $result[$name] = $this->$name;
        }
        return $result;
    }

    public function save(): void
    {
        if ($this->id !== null) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    private function insert(): void
    {
        // пустые значения не вставляем, чтобы сработали значения по умолчанию из БД
        $properties = array_filter($this->getProperties(), function ($value) {
            return $value !== null;
        });
        $columns = [];
        $names = [];
        $values = [];
        foreach ($properties as $column => $value) {
            $columns[] = '`' . $column . '`';
            $names[] = ':' . $column;
            $values[':' . $column] = $value;
        }
        $sql = 'INSERT INTO `' . static::getTableName() . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $names) . ');';
        $db = Db::getInstance();
        $db->query($sql, $values);
        $this->id = $db->getLastInsertId();
    }

    private function update(): void
    {
        $sets = [];
        $values = [':id' => $this->id];
        $i = 1;
        foreach ($this->getProperties() as $column => $value) {
            if ($column === 'id') {
                continue;
            }
            $sets[] = '`' . $column . '` = :param' . $i;
            $values[':param' . $i] = $value;
            $i++;
        }
        $sql = 'UPDATE `' . static::getTableName() . '` SET ' . implode(', ', $sets) . ' WHERE `id` = :id;';
        Db::getInstance()->query($sql, $values);
    }

    public function delete(): void
    {
        Db::getInstance()->query('DELETE FROM `' . static::getTableName() . '` WHERE `id` = :id;', [':id' => $this->id]);
        $this->id = null;
    }
}
