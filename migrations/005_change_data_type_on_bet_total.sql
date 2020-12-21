-- Используем базу данных
USE yeticave;

-- Изменение типа поля total в таблице bet на INT, т.к. значение должно быть недробным числом
ALTER TABLE bet
    CHANGE COLUMN total total INT NULL DEFAULT NULL AFTER updated_at;
