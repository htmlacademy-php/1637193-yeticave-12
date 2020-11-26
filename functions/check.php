<?php

/**
 * Функция проверяет, заполнено ли указанное поле
 * @param $name string Проверяемое поле в форме
 * @return string В случае незаполненности возвращает требование о необходимости добавить данные либо NULL
 */
function validate_filled($name)
{
    if (empty($_POST[$name])) {
        return "Это поле должно быть заполнено";
    }
    return null;
}

/**
 * Функция проверяет категорию: если категория равна "Выберите категорию", то валидация не пройдена.
 * @param string $field_name Имя поля
 * @return string|null Причина ошибки валидации или NULL при отсутствии ошибок
 **/
function validate_category(string $field_name)
{
    if ($empty = validate_filled($field_name)) {
        return $empty;
    }
    return null;
}


/**
 * Функция валидации изображения, в случае успешной валидации возвращает NULL.
 * @param string $field_name Имя поля изображения
 * @return string|null Причина ошибки валидации или NULL при отсутствии ошибок
 */
function validate_file(string $field_name): ?string
{
    if (isset($_FILES[$field_name]) && !empty($_FILES[$field_name]['name'])) {
        $file_name = $_FILES[$field_name]['tmp_name'];
        $file_size = $_FILES[$field_name]['size'];
        $type_file = mime_content_type($file_name);

        if ($type_file === 'image/jpeg' || $type_file === 'image/png' || $type_file === 'image/jpg') {
            return NULL;
        }
        if ($file_size > UPLOAD_MAX_SIZE) {
            return "Максимальный размер файла: 2 Мб";
        }
        return 'Изображение должно быть в одном из данных форматов: jpeg, jpg и png';
    } else {
        return 'Поле не заполнено';
    }
}


/**
 * Функция валидации полей с цифровым значением (начальной цены лота и шага ставки),
 * в случае успешной валидации возвращает NULL.
 * @param string $field_name Имя поля
 * @return string|null Причина ошибки валидации или NULL при отсутствии ошибок
 **/
function validate_number_value(string $field_name): ?string
{
    if ($empty = validate_filled($field_name)) {
        return $empty;
    } elseif (!is_numeric($_POST[$field_name])) {
        return 'Значение должно быть числом';
    } elseif ($_POST[$field_name] <= 0) {
        return 'Значение должно быть больше нуля';
    }
    return null;
}


/**
 * Функция валидации даты окончания лота, в случае успешной валидации возвращает NULL.
 * @param string $field_name Имя поля
 * @return string|null Причина ошибки валидации или NULL при отсутствии ошибок
 **/
function validate_date_end(string $field_name): ?string
{
    $tomorrow_date = date_create('tomorrow');

    if ($empty = validate_filled($field_name)) {
        return $empty;
    } elseif (!is_date_valid($_POST[$field_name], CORRECT_DATE_TIME)) {
        return 'Некорректный формат даты, исправьте на "ГГГГ-ММ-ДД"';
    } elseif (date_create($_POST[$field_name]) < $tomorrow_date) {
        return 'Некорректная дата завершения лота';
    }
    return null;
}
