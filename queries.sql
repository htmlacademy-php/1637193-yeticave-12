-- Используем базу данных
USE yeticave;

-- добавление в БД существующего списка категорий
INSERT INTO category
SET title         = 'Доски и лыжи',
    symbolic_code = 'boards';
INSERT INTO category
SET title         = 'Крепления',
    symbolic_code = 'attachment';
INSERT INTO category
SET title         = 'Ботинки',
    symbolic_code = 'boots';
INSERT INTO category
SET title         = 'Одежда',
    symbolic_code = 'clothing';
INSERT INTO category
SET title         = 'Инструменты',
    symbolic_code = 'tools';
INSERT INTO category
SET title         = 'Разное',
    symbolic_code = 'other';

-- добавление примеров пользователей
INSERT INTO users
SET email    = 'ivanov@mail.ru',
    name     = 'Ivanov Ivan',
    password = '12345',
    contacts = 'а/я 11 г. Ивановка, 654321';
INSERT INTO users
SET email    = 'petrov@yandex.ru',
    name     = 'Петров Пётр',
    password = 'qwerty',
    contacts = '7 Green Avenue, Apt. 4';
INSERT INTO users
SET email    = 'vasya@pisem.net',
    name     = 'Просто Вася',
    password = 'vasinpassword',
    contacts = 'Skype: prosto_vasya';

-- добавление существующего списка объявлений //попробовал альтернативным синтаксисом
INSERT item(title, description, start_price, image_url, created_at, completed_at, bet_step, author_id, category_id,
            winner_id)
VALUES ('2014 Rossignol District Snowboard', 'Snowboard', 10999, 'img/lot-1.jpg', '2020-10-20 12:00:00',
        '2020-12-20 12:00:00', 50, 1, 1, 2)
        ,
       ('DC Ply Mens 2016/2017 Snowboard', 'Snowboard', 159999, 'img/lot-2.jpg', '2020-10-21 12:00:00',
        '2020-12-21 12:00:00', 1550, 2, 1, 3)
        ,
       ('Крепления Union Contact Pro 2015 года размер L/XL', 'Крепления размер L/XL', 8000, 'img/lot-3.jpg',
        '2020-12-22 12:00:00', '2020-11-25 12:00:00', 55, 3, 2,
        1)
        ,
       ('Ботинки для сноуборда DC Mutiny Charocal', 'Ботинки для сноуборда', 10999, 'img/lot-4.jpg',
        '2020-10-23 12:00:00', '2020-12-20 12:00:00', 15, 1, 3, 2)
        ,
       ('Куртка для сноуборда DC Mutiny Charocal', 'Куртка для сноуборда', 7500, 'img/lot-5.jpg', '2020-10-24 12:00:00',
        '2020-12-10 12:00:00', 150, 2, 4, 3)
        ,
       ('Маска Oakley Canopy', 'Маска', 5400, 'img/lot-6.jpg', '2020-10-25 12:00:00', '2020-12-25 12:00:00', 20, 3, 6,
        1);
-- вопрос по author_id, category_id, winner_id: пока вставил статичные данные. Надо ли было сразу как-то настраивать через внешние данные с созданных таблиц? Как вариант вижу костыль через UPDATE ... SET ... = '...' WHERE


-- добавление примеров ставок для 1-го объявления
INSERT bet(total, user_id, item_id, created_at)
VALUES (5500, 2, 6, '2020-10-25 12:00:00'),
       (6600, 3, 6, '2020-10-26 12:00:00'),
       (7700, 1, 6, '2020-10-27 12:00:00');


-- получение всех категорий:
SELECT title, symbolic_code
FROM category;


-- получение самых новых, открытых лотов.
-- Каждый лот должен включать название, стартовую цену, ссылку на изображение, текущую цену, название категории;
SELECT item.title                                              AS 'Название категории',
       item.start_price                                        AS 'Стартовая цена',
       item.image_url                                          AS 'Ссылка на изображение',
       IF(bet.total IS NULL, item.start_price, MAX(bet.total)) AS 'Текущая цена',
       item.created_at                                         AS 'Дата и время создания лота',
       item.completed_at                                       AS 'Дата и время завершения лота',
       category.title                                          AS 'Название категории'
FROM item
         INNER JOIN category ON item.category_id = category.id
         LEFT JOIN bet ON item.id = bet.item_id
WHERE item.completed_at > NOW()
GROUP BY item.id
ORDER BY item.created_at DESC;


-- показ лота по его id. Получение названия категории, к которой принадлежит лот;
SELECT item.*, category.title AS category_title
FROM item
         LEFT JOIN category ON category.id = item.category_id
WHERE item.id = 3;


-- обновление названия лота по его идентификатору;
UPDATE item
SET item.title = 'Такой-то лот'
WHERE item.id = 2;

-- получение списка ставок для лота по его идентификатору с сортировкой по дате.
SELECT users.name     AS 'Пользователь',
       item.title     AS 'Название товара в лоте',
       bet.total      AS 'Размер ставки',
       bet.created_at AS 'Дата и время ставки'
FROM bet
         INNER JOIN users ON bet.user_id = users.id
         INNER JOIN item ON bet.item_id = item.id
WHERE bet.item_id = 6
ORDER BY bet.created_at ASC;

