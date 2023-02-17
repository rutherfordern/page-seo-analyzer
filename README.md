## Описание
[![Maintainability](https://api.codeclimate.com/v1/badges/dc592ba08875058f14ec/maintainability)](https://codeclimate.com/github/rutherfordern/page-seo-analyzer/maintainability)

**Анализатор страниц** - веб-приложение на базе фреймворка **Slim**, которое позволяет узнать код HTTP-ответа, содержимое элементов h1, title и метатега description указанной страницы.

Для работы с базой данных используется PDO. СУБД - PostgreSQL. 
Фронтенд - Bootstrap.
## Требования
- PHP >= 8.1
- Composer

## Установка для локальной работы
1. Склонируйте проект, затем выполните установку.
```sh
make install
```
2. Создайте .env файл по примеру .env.example и укажите нужные данные.
3. Создайте таблицы по примеру файла database.sql - вручную или с помощью запуска данного файла.
4. Выполните запуск проекта и перейдите по адресу http://localhost:8080
```sh
make start-local
```
![mainpage](https://cdn2.hexlet.io/derivations/image/original/eyJpZCI6IjQ0YTU5ZGM5ZjdiOTBlYjlkZTEwNTgzZThiZGM5YjE2LnBuZyIsInN0b3JhZ2UiOiJjYWNoZSJ9?signature=f5abe619c1ac3a013db5992f289435454f903940ff8b10a4d4563dcdc9d71a8f)

![urls](https://cdn2.hexlet.io/derivations/image/original/eyJpZCI6IjA2NzMxZWY4N2QyMDU4OTVhZGU3NDAzZjllZjc5ZjIyLnBuZyIsInN0b3JhZ2UiOiJjYWNoZSJ9?signature=6b03e29a524b277f455421c73075974e73b382edba228260c4236089b44c169b)
Вместо keywords будет title.
![url](https://cdn2.hexlet.io/derivations/image/original/eyJpZCI6IjM2ZmNmMzI0Y2EyN2RhYTg2NjgyMTNiMWFjZjA4M2ZkLnBuZyIsInN0b3JhZ2UiOiJjYWNoZSJ9?signature=4d813fa6c9c4c09a9c1d6e5e2f8407faab1ee4736529cb066ee24deaa77559d4)
