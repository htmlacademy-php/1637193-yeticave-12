-- Создание базы данных
CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE UTF8_GENERAL_CI;

-- Используем базу данных
USE yeticave;

-- Создание таблицы категорий
CREATE TABLE category
(
    category_id    INT          NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    category_title VARCHAR(100) NOT NULL,
    symbolic_code  VARCHAR(100) NOT NULL,
    INDEX (category_title) -- создаю индекс для поля, по которому будет поиск
);

-- Создание таблицы с лотами аукциона
CREATE TABLE item
(
    item_id         INT          NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    creation_date   DATETIME,
    item_title      VARCHAR(100) NOT NULL,
    description     VARCHAR(255) NOT NULL,
    image_url       VARCHAR(255) NOT NULL,
    start_price     INT          NOT NULL,
    completion_date DATETIME     NOT NULL,
    bet_step        INT          NOT NULL,
    INDEX (item_title) -- создаю индекс для поля, по которому будет поиск
);

-- Создание таблицы со ставками
CREATE TABLE bet
(
    bet_id   INT            NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    bet_date DATETIME,
    total    DECIMAL(10, 2) NOT NULL
);

-- Создание таблицы с пользователями
CREATE TABLE users
(
    user_id           INT          NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    registration_date DATETIME     NOT NULL,
    email             VARCHAR(100) NOT NULL,
    name              VARCHAR(45)  NOT NULL,
    password          VARCHAR(45)  NOT NULL,
    contacts          VARCHAR(255) NOT NULL,
    INDEX (email) -- создаю индекс для поля, по которому будет поиск
);

-- Создание таблицы связей с авторами и их лотами
CREATE TABLE author_item
(
    author_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    user_id   INT NOT NULL,
    item_id   INT NOT NULL,
    INDEX (user_id), -- создаю индекс для поля, по которому будет поиск
    INDEX (item_id), -- создаю индекс для поля, по которому будет поиск
    CONSTRAINT users -- ограничение внешнего ключа
        FOREIGN KEY (user_id) -- указываю внешний ключ для поля
            REFERENCES users (user_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE,
    CONSTRAINT item -- ограничение внешнего ключа
        FOREIGN KEY (item_id) -- указываю внешний ключ для поля
            REFERENCES item (item_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE
);

-- Создание таблицы связей с победителями и их лотами
CREATE TABLE winner_item
(
    winner_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    user_id   INT NOT NULL,
    item_id   INT NOT NULL,
    INDEX (user_id), -- создаю индекс для поля, по которому будет поиск
    INDEX (item_id), -- создаю индекс для поля, по которому будет поиск
    CONSTRAINT users -- ограничение внешнего ключа
        FOREIGN KEY (user_id) -- указываю внешний ключ для поля
            REFERENCES users (user_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE, -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT item -- ограничение внешнего ключа
        FOREIGN KEY (item_id) -- указываю внешний ключ для поля
            REFERENCES item (item_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE -- автоматическое обновление записи по ссылке после обновления в первоисточнике
);

-- Создание таблицы связей с категориями лотов и ставками
CREATE TABLE category_item
(
    category_item_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    category_id      INT NOT NULL,
    item_id          INT NOT NULL,
    INDEX (category_id), -- создаю индекс для поля, по которому будет поиск
    INDEX (item_id), -- создаю индекс для поля, по которому будет поиск
    CONSTRAINT category -- ограничение внешнего ключа
        FOREIGN KEY (category_id) -- указываю внешний ключ для поля
            REFERENCES category (category_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE, -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT item -- ограничение внешнего ключа
        FOREIGN KEY (item_id) -- указываю внешний ключ для поля
            REFERENCES item (item_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE -- автоматическое обновление записи по ссылке после обновления в первоисточнике
);


-- Создание таблицы связей с ставками от пользователей
CREATE TABLE IF NOT EXISTS user_bet
(
    user_bet_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    user_id     INT NOT NULL,
    bet_id      INT NOT NULL,
    item_id     INT NOT NULL,
    INDEX (user_id), -- создаю индекс для поля, по которому будет поиск
    INDEX (bet_id), -- создаю индекс для поля, по которому будет поиск
    INDEX (item_id), -- создаю индекс для поля, по которому будет поиск
    CONSTRAINT users -- ограничение внешнего ключа
        FOREIGN KEY (user_id) -- указываю внешний ключ для поля
            REFERENCES users (user_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE, -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT bet -- ограничение внешнего ключа
        FOREIGN KEY (bet_id) -- указываю внешний ключ для поля
            REFERENCES bet (bet_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE, -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT item -- ограничение внешнего ключа
        FOREIGN KEY (item_id) -- указываю внешний ключ для поля
            REFERENCES item (item_id)
            ON DELETE CASCADE -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE -- автоматическое обновление записи по ссылке после обновления в первоисточнике
);
