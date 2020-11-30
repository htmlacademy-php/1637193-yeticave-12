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
        '2020-10-22 12:00:00', '2020-11-25 12:00:00', 55, 3, 2,
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
       (7700, 1, 6, '2020-10-27 12:00:00'),
       (11000, 1, 5, '2020-10-27 11:00:00'),
       (12000, 2, 5, '2020-10-27 12:00:00'),
       (15000, 1, 4, '2020-10-27 11:00:00'),
       (16000, 2, 4, '2020-10-27 12:00:00'),
       (11000, 3, 3, '2020-10-27 11:00:00'),
       (12000, 2, 3, '2020-10-27 12:00:00'),
       (17000, 1, 2, '2020-10-27 11:00:00'),
       (18000, 2, 2, '2020-10-27 12:00:00'),
       (21000, 3, 1, '2020-10-27 11:00:00'),
       (22000, 1, 1, '2020-10-27 12:00:00');


-- получение всех категорий:
SELECT title, symbolic_code
FROM category;


-- получение самых новых, открытых лотов.
-- Каждый лот должен включать название, стартовую цену, ссылку на изображение, текущую цену, название категории;
SELECT item.title                                              AS 'title',
       item.start_price                                        AS 'start_price ',
       item.image_url                                          AS 'image_url',
       IFNULL(MAX(bet.total), item.start_price)                AS 'current_price',
       item.created_at                                         AS 'created_at',
       item.completed_at                                       AS 'completed_at ',
       category.title                                          AS 'category_title'
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

-- Изменение типа поля description на TEXT, т.к. VARCHAR(255) мал по размеру
ALTER TABLE item
CHANGE description description TEXT
    CHARACTER SET utf8
    COLLATE utf8_general_ci NOT NULL;


-- Добавление большего по размеру описания для лотов после изменения типа поля
UPDATE item
SET item.description = 'Lorem ipsum dolor, sit amet, consectetur adipisicing elit. Veritatis alias quam aperiam, earum sunt, nulla id quis cum totam eaque deleniti qui eligendi nisi beatae? Nisi reprehenderit debitis error voluptate.
Nulla doloribus repudiandae velit dolore distinctio assumenda nihil, sequi ipsa tenetur dolores, aliquam saepe numquam magni mollitia exercitationem eveniet atque accusamus expedita molestias vero qui non! Voluptate aperiam dolores assumenda.
Sunt nihil in, similique vero laborum id exercitationem. Ratione corrupti eum voluptate itaque, dolorem temporibus doloremque voluptatum incidunt odio animi officiis ipsa ullam dignissimos. Accusantium omnis, eius reiciendis nesciunt vero!'
WHERE item.id BETWEEN 1 AND 6;

-- Увеличение количества символов в поле "Пароль" для сохранения ХЕШ-пароля в БД
ALTER TABLE users
CHANGE password
    password VARCHAR(60)
    CHARACTER SET utf8
    COLLATE utf8_general_ci NULL DEFAULT NULL;
