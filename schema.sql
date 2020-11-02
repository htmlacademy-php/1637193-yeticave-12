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
    INDEX (category_title)                                           -- создаю индекс для поля, по которому будет поиск
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
    author_id       INT          NOT NULL,
    category_id     INT          NOT NULL,
    winner_id       INT          NOT NULL,
    INDEX item_item_title (item_title),                               -- создаю индекс для поля, по которому будет поиск
    INDEX item_category_id (category_id),                             -- создаю индекс для поля, по которому будет поиск
    INDEX item_author_id (author_id),                                 -- создаю индекс для поля, по которому будет поиск
    CONSTRAINT category                                               -- ограничение внешнего ключа
        FOREIGN KEY (category_id)                                     -- указываю внешний ключ для поля
            REFERENCES category (category_id)
            ON DELETE CASCADE                                         -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE,                                        -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT users                                                  -- ограничение внешнего ключа
        FOREIGN KEY (author_id)                                       -- указываю внешний ключ для поля
            REFERENCES users (user_id)
            ON DELETE CASCADE                                         -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE,                                        -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT users                                                  -- ограничение внешнего ключа
        FOREIGN KEY (winner_id)                                       -- указываю внешний ключ для поля
            REFERENCES users (user_id)
            ON DELETE CASCADE                                         -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE                                         -- автоматическое обновление записи по ссылке после обновления в первоисточнике
);

-- Создание таблицы со ставками
CREATE TABLE bet
(
    bet_id   INT            NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    bet_date DATETIME       NOT NULL,
    total    DECIMAL(10, 2) NOT NULL,
    user_id  INT            NOT NULL,
    item_id  INT            NOT NULL,
    INDEX bet_user_id (user_id),                                 -- создаю индекс для поля, по которому будет поиск
    INDEX bet_item_id (item_id),                                 -- создаю индекс для поля, по которому будет поиск
    CONSTRAINT item                                              -- ограничение внешнего ключа
        FOREIGN KEY (item_id)                               -- указываю внешний ключ для поля
            REFERENCES item (item_id)
            ON DELETE CASCADE                                    -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE,                                   -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT users                                             -- ограничение внешнего ключа
        FOREIGN KEY (user_id)                              -- указываю внешний ключ для поля
            REFERENCES users (user_id)
            ON DELETE CASCADE                                    -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE                                    -- автоматическое обновление записи по ссылке после обновления в первоисточнике
);

-- Создание таблицы с пользователями
CREATE TABLE users
(
    user_id           INT          NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    registration_date DATETIME     NOT NULL,
    email             VARCHAR(100) NOT NULL UNIQUE,
    name              VARCHAR(45)  NOT NULL,
    password          VARCHAR(45)  NOT NULL,
    contacts          VARCHAR(255) NOT NULL,
    INDEX users_user_id (user_id)                                       -- создаю индекс для поля, по которому будет поиск
);





