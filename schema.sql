-- Создание базы данных
CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE UTF8_GENERAL_CI;

-- Используем базу данных
USE yeticave;

-- Создание таблицы категорий
CREATE TABLE category
(
    id            INT          NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    title         VARCHAR(100),                            -- Заголовок категорий лотов
    symbolic_code VARCHAR(100),                            --  Символьный код категории для назначения правильного класса в меню категорий
    INDEX category_title (title)                                    -- создаю индекс для поля, по которому будет поиск
);

-- Создание таблицы с пользователями
CREATE TABLE users
(
    id         INT          NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    created_at TIMESTAMP DEFAULT NOW(),                          -- дата регистрации: дата и время, когда этот пользователь завёл аккаунт
    updated_at TIMESTAMP ON UPDATE NOW(),                        -- дата и время обновления таблицы
    email      VARCHAR(100) UNIQUE,                     -- email пользователя
    name       VARCHAR(45),                            -- имя пользователя
    password   VARCHAR(45),                            -- пароль: хэшированный пароль пользователя
    contacts   VARCHAR(255),                            -- контакты: контактная информация для связи с пользователем
    INDEX users_id (id)                                          -- создаю индекс для поля, по которому будет поиск
);

-- Создание таблицы с лотами аукциона
CREATE TABLE item
(
    id           INT          NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    created_at   TIMESTAMP DEFAULT NOW(),                          -- дата создания: дата и время, когда этот лот был создан пользователем
    updated_at   TIMESTAMP ON UPDATE NOW(),                        -- дата и время обновления таблицы
    title        VARCHAR(100),                            -- название: задается пользователем
    description  VARCHAR(255),                            -- описание: задается пользователем
    image_url    VARCHAR(255),                            -- изображение: ссылка на файл изображения
    start_price  INT,                            -- начальная цена лота
    completed_at TIMESTAMP,                                        -- дата завершения аукциона по данному лоту
    bet_step     INT,                            -- шаг ставки
    author_id    INT,                            -- связь: - автор: пользователь, создавший лот;
    category_id  INT,                            -- связь: - победитель: пользователь, выигравший лот
    winner_id    INT,                            -- связь: - категория: категория объявления
    INDEX item_title (title),                                      -- создаю индекс для поля, по которому будет поиск
    INDEX item_category_id (category_id),                          -- создаю индекс для поля, по которому будет поиск
    INDEX item_author_id (author_id),                              -- создаю индекс для поля, по которому будет поиск
    CONSTRAINT item_category_idx                                   -- ограничение внешнего ключа
        FOREIGN KEY (category_id)                                  -- указываю внешний ключ для поля
            REFERENCES category (id)
            ON DELETE CASCADE                                      -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE,                                     -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT item_author_idx                                     -- ограничение внешнего ключа
        FOREIGN KEY (author_id)                                    -- указываю внешний ключ для поля
            REFERENCES users (id)
            ON DELETE CASCADE                                      -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE,                                     -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT item_winner_idx                                     -- ограничение внешнего ключа
        FOREIGN KEY (winner_id)                                    -- указываю внешний ключ для поля
            REFERENCES users (id)
            ON DELETE CASCADE                                      -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE                                      -- автоматическое обновление записи по ссылке после обновления в первоисточнике
);

-- Создание таблицы со ставками
CREATE TABLE bet
(
    id         INT            NOT NULL AUTO_INCREMENT PRIMARY KEY, -- первичный ключ, автоматически увеличивается на 1 для новой записи
    created_at TIMESTAMP DEFAULT NOW(),                            -- дата и время размещения ставки
    updated_at TIMESTAMP ON UPDATE NOW(),                          -- дата и время обновления таблицы
    total      DECIMAL(10, 2),                            -- сумма: цена, по которой пользователь готов приобрести лот
    user_id    INT,                            -- связь: - пользователь, сделавший ставку
    item_id    INT,                            -- связь: - лот, на который сделали ставку
    INDEX bet_user_id (user_id),                                   -- создаю индекс для поля, по которому будет поиск
    INDEX bet_item_id (item_id),                                   -- создаю индекс для поля, по которому будет поиск
    CONSTRAINT bet_item_idx                                        -- ограничение внешнего ключа
        FOREIGN KEY (item_id)                                      -- указываю внешний ключ для поля
            REFERENCES item (id)
            ON DELETE CASCADE                                      -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE,                                     -- автоматическое обновление записи по ссылке после обновления в первоисточнике
    CONSTRAINT bet_user_idx                                        -- ограничение внешнего ключа
        FOREIGN KEY (user_id)                                      -- указываю внешний ключ для поля
            REFERENCES users (id)
            ON DELETE CASCADE                                      -- автоматическое удаление записи по ссылке после удаления в первоисточнике
            ON UPDATE CASCADE                                      -- автоматическое обновление записи по ссылке после обновления в первоисточнике
);







