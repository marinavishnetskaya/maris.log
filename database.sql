-- База данных для проекта mari$ bo$$ (OSPanel -> phpMyAdmin -> вкладка "Импорт", или вставьте в вкладку SQL)
CREATE DATABASE IF NOT EXISTS `saktaganova` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `saktaganova`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nickname` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `is_confirmed` TINYINT(1) NOT NULL DEFAULT 0,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` VARCHAR(20) NOT NULL DEFAULT 'user',
  `authtoken` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `articles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `author_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `text` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `img` VARCHAR(255) DEFAULT NULL,
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Тестовые данные: пользователь admin / пароль 12345678
INSERT INTO `users` (`nickname`, `email`, `is_confirmed`, `password_hash`, `role`, `authtoken`)
SELECT 'admin', 'admin@example.com', 1, '$2y$10$NWYD1wp.TPHYkaOx33RxCO2tIVjFbRD3HPwzarm30EwH6AdMivw6W', 'admin', 'initialtoken'
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `nickname` = 'admin');

INSERT INTO `articles` (`author_id`, `name`, `text`, `img`)
SELECT u.id, 'Белый гриб', 'Белый гриб (Boletus edulis) — один из самых ценных съедобных грибов.', 'uploads/(Gemeine_Steinpilz)_Boletus_edulis.jpg'
FROM `users` u WHERE u.nickname = 'admin' AND NOT EXISTS (SELECT 1 FROM `articles`);

INSERT INTO `articles` (`author_id`, `name`, `text`, `img`)
SELECT u.id, 'Вторая статья', 'Пример статьи с картинкой для проверки стилей и загрузки изображений.', 'uploads/square_1280_afee47aee125cf551c76d086d5616074.jpg'
FROM `users` u WHERE u.nickname = 'admin' AND (SELECT COUNT(*) FROM `articles`) = 1;
