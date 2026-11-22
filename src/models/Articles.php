<?php

namespace src\models;

use src\services\Db;
use src\exceptions\InvalidArgumentException;

class Articles extends ActiveRecordEntity
{
    protected $author_id;
    protected $name;
    protected $text;
    protected $created_at;
    protected $img;

    protected static function getTableName(): string
    {
        return 'articles';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getText(): string
    {
        return $this->text;
    }

    // Короткий кусок текста для списка статей (без html-экранирования, его делает шаблон)
    public function getExcerpt(int $length = 150): string
    {
        return mb_strimwidth($this->text, 0, $length, '...');
    }

    public function getDate(): string
    {
        return date('d.m.Y', strtotime($this->created_at));
    }

    public function getAuthorId(): int
    {
        return (int) $this->author_id;
    }

    public function getAuthor(): ?Users
    {
        return Users::getById($this->author_id);
    }

    public function getAuthorName(): string
    {
        $author = $this->getAuthor();
        return $author !== null ? $author->getNickname() : 'неизвестен';
    }

    public function getImg(): ?string
    {
        return $this->img ?: null;
    }

    // Менять и удалять статью может её автор или админ
    public function isEditableBy(?Users $user): bool
    {
        return $user !== null && ($user->isAdmin() || $user->getId() === $this->getAuthorId());
    }

    // Новые статьи сверху
    public static function findAll(): array
    {
        return Db::getInstance()->query('SELECT * FROM `articles` ORDER BY `id` DESC;', [], static::class);
    }

    public static function findLatest(int $limit): array
    {
        return Db::getInstance()->query('SELECT * FROM `articles` ORDER BY `id` DESC LIMIT ' . (int) $limit . ';', [], static::class);
    }

    // Поиск по названию и тексту
    public static function search(string $searchString): array
    {
        $like = '%' . $searchString . '%';
        return Db::getInstance()->query(
            'SELECT * FROM `articles` WHERE `name` LIKE :name OR `text` LIKE :text ORDER BY `id` DESC;',
            [':name' => $like, ':text' => $like],
            static::class
        );
    }

    // Проверяем название и текст, возвращаем их очищенными от пробелов по краям
    private static function checkFields(array $fields): array
    {
        $name = isset($fields['name']) && is_string($fields['name']) ? trim($fields['name']) : '';
        $text = isset($fields['text']) && is_string($fields['text']) ? trim($fields['text']) : '';
        if ($name === '') {
            throw new InvalidArgumentException('Не передано название статьи');
        }
        if (mb_strlen($name) > 255) {
            throw new InvalidArgumentException('Название не длиннее 255 символов');
        }
        if ($text === '') {
            throw new InvalidArgumentException('Не передан текст статьи');
        }
        return [$name, $text];
    }

    // Сохраняет картинку в uploads/ и возвращает путь. Если файл не выбран, вернёт null.
    private static function saveImage(?array $file): ?string
    {
        if (empty($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            throw new InvalidArgumentException('Слишком большой файл');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Ошибка при загрузке файла');
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Слишком большой файл! Должно быть не более 5 МБ');
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowed, true) || @getimagesize($file['tmp_name']) === false) {
            throw new InvalidArgumentException('Можно загружать только картинки: jpg, png, gif, webp');
        }

        $folder = dirname(__DIR__, 2) . '/uploads';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }
        $fileName = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $folder . '/' . $fileName)) {
            throw new InvalidArgumentException('Не удалось сохранить файл. Проверьте, что в папку uploads можно писать');
        }
        return 'uploads/' . $fileName;
    }

    public static function create(array $fields, ?array $imgFile, Users $author): Articles
    {
        [$name, $text] = self::checkFields($fields);
        $img = self::saveImage($imgFile);

        $article = new Articles();
        $article->name = $name;
        $article->text = $text;
        $article->author_id = $author->getId();
        $article->img = $img;
        $article->save();
        return $article;
    }

    public function updateFromArray(array $fields, ?array $imgFile = null): Articles
    {
        [$name, $text] = self::checkFields($fields);
        $img = self::saveImage($imgFile);

        $this->name = $name;
        $this->text = $text;
        if ($img !== null) {
            $this->img = $img;
        }
        $this->save();
        return $this;
    }
}
