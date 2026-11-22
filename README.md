# mari$ bo$$ — блог на PHP

## Запуск на OSPanel

1. Скопируйте папку проекта в `domains` OSPanel.
   - Как домен: `domains/saktaganova.log` → http://saktaganova.log/
   - Или в подпапку localhost: `domains/localhost/saktaganova.log-main` → http://localhost/saktaganova.log-main/

   Пути определяются автоматически, ничего менять не нужно.
2. В настройках OSPanel выберите PHP **7.4 или новее** (подойдёт и 8.x) и включите Apache (нужен mod_rewrite для `.htaccess`).
3. Создайте базу: откройте в браузере `/install.php` (например http://localhost/saktaganova.log-main/install.php). Страница сама создаст БД `saktaganova`, таблицы и тестовые данные. Если есть phpMyAdmin, можно вместо этого импортировать `database.sql`. После установки `install.php` можно удалить.
4. Если у MySQL root задан пароль — впишите его в `src/config/settings.php`.
5. Вход: логин `admin`, пароль `12345678` (или зарегистрируйтесь).
6. Папка `uploads/` должна быть доступна на запись (для загрузки обложек).

## Что умеет сайт

- Главная, список статей, просмотр статьи, поиск по названию и тексту.
- Регистрация и вход.
- Добавлять статьи могут все вошедшие пользователи; менять и удалять — только автор статьи или админ.
- К статье можно прикрепить картинку (jpg, png, gif, webp до 5 МБ).

## Структура

- `index.php` — вход на сайт, маршрутизация
- `src/config` — настройки БД и маршруты
- `src/controllers`, `src/models`, `src/views` — MVC
- `style.css` — все стили сайта (главный цвет — `--accent` в начале файла)
