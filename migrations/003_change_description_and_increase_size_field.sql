-- Используем базу данных
USE yeticave;

-- Изменение типа поля description в таблице item на TEXT, т.к. VARCHAR(255) мал по размеру
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

-- Увеличение количества символов в поле "Пароль" в таблице users для сохранения ХЕШ-пароля в БД
ALTER TABLE users
    CHANGE password
        password VARCHAR(60)
            CHARACTER SET utf8
                COLLATE utf8_general_ci NULL DEFAULT NULL;

-- Добавление 1го тестового аккаунта для проверки авторизации
INSERT INTO users (email, name, password, contacts) VALUES ('test@test.ru', 'test account', '90b1f057fbcaf3d1a42dd905ab4e35de', 'пароль от аккаунта: password123 . Хеш получен с сайта http://md5-online.ru/')
