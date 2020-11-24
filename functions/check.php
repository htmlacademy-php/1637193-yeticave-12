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
 * Функция проверяет количество символов, введенных в поле, и сравнивает их с заданным
 * @param $name string Проверяемое поле в форме
 * @param $min int Минимальное количество символов, которое должно быть введено в данное поле
 * @param $max int Максимальное количество символов, которое должно быть введено в данное поле
 * @return string В случае неправильного числа символов в поле возвращает подсказку с необходимым количеством символов в поле либо NULL
 */
function is_correct_length($name, $min, $max)
{
    if ($name) {
        $len = strlen($_POST[$name]);
        if ($len < $min or $len > $max) {
            return "Значение должно быть от $min до $max символов";
        }
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
    } elseif ($_POST[$field_name] == 'Выберите категорию') {
        return "Выберите категорию";
    }
    return null;
}


/**
 * Функция валидации изображения, в случае успешной валидации возвращает NULL.
 * @param string $field_name Имя поля изображения
 * @param array $allowed_mime_types Массив строк с разрешенным MIME типами изображений
 * @return string|null Причина ошибки валидации или NULL при отсутствии ошибок
 */
function validate_image(string $field_name, array $allowed_mime_types): ?string
{

    if (isset($_FILES[$field_name])) {
        if ($_FILES[$field_name]['error'] == 4) {
            return 'Добавьте изображение лота';
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $path = $_FILES[$field_name]['tmp_name'];
        $mime_type = mime_content_type($path);

        if (!in_array($mime_type, $allowed_mime_types)) {
            return 'Изображение должно быть в одном из следующих форматов: ' . implode(", ", $allowed_mime_types);
        }
    }
    return null;
}

function validate_file(string $name, string $name_folder_uploads_file): ?string {
    if (isset($_FILES[$name]) && !empty($_FILES[$name]['name'])) {
        $file_name = $_FILES[$name]['tmp_name'];
        $file_path = sys_get_temp_dir();

        $type_file = mime_content_type($file_name);
        if ($type_file === 'image/jpeg' || $type_file === 'image/png' || $type_file === 'image/jpg') {
            return NULL;
        }
        else {
            return 'Допустимы только файлы изображений типов jpeg, jpg и png ';
        }
    }
    else {
        return 'Поле не заполнено ';
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
function validate_date_end(string $field_name) : ?string {
    $tomorrow_date = date_create('tomorrow');

    if($empty = validate_filled($field_name)){
        return $empty;
    } elseif(!is_only_date_valid($_POST[$field_name])) {
        return 'Некорректный формат даты, исправьте на "ГГГГ-ММ-ДД"';
    } elseif (date_create($_POST[$field_name]) < $tomorrow_date) {
        return 'Некорректная дата завершения лота';
    }
    return null;
}
