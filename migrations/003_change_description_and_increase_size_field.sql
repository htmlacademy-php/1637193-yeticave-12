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
        password VARCHAR(255)
            CHARACTER SET utf8
                COLLATE utf8_general_ci NULL DEFAULT NULL;

-- Добавление 1го тестового аккаунта для проверки авторизации
INSERT INTO users (email, name, password, contacts) VALUES ('test@test.ru', 'test account', '$2y$10$SvMpQxMkbOtv3LTN5iQ4M.C62oE0O9Jwr7wkNs5/XsGwrjpyToJI.', 'пароль от аккаунта: Pa$$w0rd!')

-- Обновление хешей пароля и контактных данных у первых трех тестовых аккаунтов:
UPDATE users SET users.password = '$2y$10$la4wtdluoOwRMcyhj1/VeugC333KXC3e/ZTmNrDQH4FsD.m0noNvS', users.contacts = 'Пароль от аккаунта: 12345qwer' WHERE users.id = 1;
UPDATE users SET users.password = '$2y$10$CpHYX2mBDOEjDAAKB1.jS.VOM51ZcVq1sdY39BLt1.M0jF32/nDLu', users.contacts = 'Пароль от аккаунта: qwerty123' WHERE users.id = 2;
UPDATE users SET users.password = '$2y$10$2riOGlP98L6IGj4s4Ag3HeZXIasVTlYAJqQnm1hQzYoWQtaDkrpn6', users.contacts = 'Пароль от аккаунта: vasinpassword123' WHERE users.id = 3;
