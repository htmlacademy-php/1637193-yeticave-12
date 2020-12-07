use yeticave;

-- Создание полнотекстового индекса для полей «название» и «описание» в таблице лотов
CREATE FULLTEXT INDEX item_search ON item(title,description);
CREATE FULLTEXT INDEX item_title_search ON item(title);
CREATE FULLTEXT INDEX item_description_search ON item(description);
