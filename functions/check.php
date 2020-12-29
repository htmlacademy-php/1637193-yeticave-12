<?php

/**
 * Функция перенаправляет на страницу вывода ошибки с сооветствующим текстом описания ошибки
 * @param int $http_code Код состояния HTTP
 * @param string $redirect_page страница, с которой было перенаправление в ходе ошибки, без .php в конце [необязательный параметр]
 * return string Строка с описанием ошибок, перенаправляющая на страницу вывода ошибки.
 */
function error_output(int $http_code, string $redirect_page = ''): string
{
    http_response_code($http_code);
    $error = 'Произошла ошибка: ' . $http_code . ' &#129298; ';
    $error_description = 'Такой страницы не существует на сайте.';
    $error_link = '/index.php';
    $error_link_description = 'Предлагаем вернуться на главную.';

    if ($http_code === 403) {
        switch ($redirect_page) {
            case 'add':
                $error_description = 'Для добавления лота необходимо пройти регистрацию на сайте.';
                $error_link = '/sign-up.php';
                $error_link_description = 'Зарегистрироваться можно по этой ссылке.';
                break;
            case 'my_bets':
                $error_description = 'Для просмотра сделанных ставок необходимо пройти авторизацию на сайте.';
                $error_link = '/enter.php';
                $error_link_description = 'Авторизоваться можно по этой ссылке.';
                break;
            case 'enter':
                $error_description = 'Вы уже авторизованы на нашем сайте. &#128517;';
                break;
            default:
                $error_description = 'Вы уже зарегистрированы на нашем сайте. &#128517;';
                break;
        }
    } elseif ($http_code >= 500) {
        $error_description = 'У нас произошла внутренняя техническая ошибка сервера. &#128532;';
        $error_link_description = 'Возвращайтесь к нам немного позже.';
    }

    return include_template_error($error, $error_description, $error_link, $error_link_description, $http_code);
}

/**
 * Функция проверяет, заполнено ли указанное поле
 * @param $name string Проверяемое поле в форме
 * @param $name_in_russian string Название/описание поля на русском языке
 * @return string|null В случае незаполненности возвращает требование о необходимости добавить данные либо null
 */
function validate_filled(string $name, string $name_in_russian): ?string
{
    if (empty($_POST[$name])) {
        if (!$name_in_russian) {
            return "Данное поле должно быть заполнено";
        }
        return "Необходимо заполнить поле " . '"' . $name_in_russian . '"';
    }
    return null;
}

/**
 * Функция проверяет, выбрал ли пользователь категорию при добавлении лота
 * @param string $category_name Поле в форме с выбором категорий
 * @return string|null Причина ошибки валидации или null при отсутствии ошибок
 **/
function validate_category(string $category_name): ?string
{
    if (empty($_POST[$category_name])) {
        return "Необходимо выбрать категорию у добавляемого лота";
    }
    return null;
}

/**
 * Функция валидации сохранения файла изображения, в случае успешной валидации возвращает null.
 * @param string $file Поле в форме, где выбирается файл изображения c ПК пользователя
 * @return string|null Причина ошибки валидации или null при отсутствии ошибок
 */
function validate_file_before_saving(string $file): ?string
{
    if (isset($_FILES[$file]) && !empty($_FILES[$file]['name'])) {
        $file_name = $_FILES[$file]['tmp_name'];
        $file_size = $_FILES[$file]['size'];
        $type_file = mime_content_type($file_name);

        if (($type_file === 'image/jpeg' || $type_file === 'image/png' || $type_file === 'image/jpg')
            && ($file_size <= UPLOAD_MAX_SIZE)) {
            return null;
        }
        if ($file_size > UPLOAD_MAX_SIZE) {
            return "Максимальный размер файла: 2 Мб";
        }
        return 'Изображение должно быть в одном из данных форматов: jpeg, jpg и png';
    }
    return 'Поле не заполнено';
}


/**
 * Функция валидации полей с цифровым значением (начальной цены лота и шага ставки),
 * в случае успешной валидации возвращает null.
 * @param string $number Поле в форме для ввода числа
 * @return string|null Причина ошибки валидации или null при отсутствии ошибок
 **/
function validate_number_value(string $number): ?string
{
    if ($empty = validate_filled($number, '')) {
        return $empty;
    } elseif (!is_numeric($_POST[$number])) {
        return 'Значение должно быть числом';
    } elseif ($_POST[$number] <= 0) {
        return 'Значение должно быть больше нуля';
    }
    return null;
}


/**
 * Функция валидации даты окончания лота, в случае успешной валидации возвращает null.
 * @param string $end_date Поле в форме, где выбирается окончание лота
 * @return string|null Причина ошибки валидации или null при отсутствии ошибок
 **/
function validate_date_end(string $end_date): ?string
{
    $tomorrow_date = date_create('tomorrow');

    if ($empty = validate_filled($end_date, 'Дата окончания торгов')) {
        return $empty;
    } elseif (!is_date_valid($_POST[$end_date], CORRECT_DATE_TIME)) {
        return 'Некорректный формат даты, исправьте на "ГГГГ-ММ-ДД"';
    } elseif (date_create($_POST[$end_date]) < $tomorrow_date) {
        return 'Некорректная дата завершения лота';
    }
    return null;
}

/**
 * Функция валидации пароля, в случае успешной валидации возвращает null
 * @param string $password Введенный пароль пользователя
 * @return string|null Причина ошибки валидации или null при отсутствии ошибок
 */
function validate_password(string $password): ?string
{
    if (empty($password)) {
        return "Придумайте и введите пароль для вашего аккаунта";
    }
    if (strlen($password) < 8) {
        return "Придуманный пароль должен быть не менее 8 символов, попробуйте дополнить.";
    }
    if (preg_match("([а-яА-ЯёЁ]+)", $password)) {
        return "В придуманном пароле не должно быть букв из кириллицы: допустимы только латинские буквы, цифры и спец. символы";
    }
    if (!preg_match("([0-9]+)", $password)) {
        return "В введенном пароле не хватает цифр";
    }
    if (!preg_match("/([a-zA-Z]+)/", $password)) {
        return "В введенном пароле не хватает латинских букв";
    }
    return null;
}

/**
 * Функция валидации адреса электронной почты, в случае успешной валидации возвращает null
 * @param $email string Введенная почта пользователя
 * @return string|null Причина ошибки валидации или null при отсутствии ошибок
 */
function validate_email(string $email): ?string
{
    if (empty($email)) {
        return "Введите адрес электронной почты";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Введите корректный e-mail в формате name@post.com";
    }
    return null;
}


/**
 * Функция валидации уникальности адреса электронной почты, в случае успешной валидации возвращает null
 * @param mysqli $connect Данные о подключении к БД
 * @param string $check_email Введенный в форму e-mail пользователя
 * @return string|null Сообщение о том, что пользователь под данным e-mail уже зарегистрирован или null при отсутствии ошибок
 */
function validate_unique_email(mysqli $connect, string $check_email): ?string
{
    $check_result = verify_existence_email_db($connect, $check_email);

    if (mysqli_num_rows($check_result) > 0) {
        return 'Пользователь с этим email уже зарегистрирован';
    }
    return null;
}

/**
 * Функция валидации контактных данных пользователя, в случае успешной валидации возвращает null
 * @param $contacts string Контактные данные пользователя
 * @return string|null Причина ошибки валидации или null при отсутствии ошибок
 */
function validate_contacts(string $contacts): ?string
{
    if (empty($contacts)) {
        return "Оставьте свои контактные данные для связи";
    }
    if (strlen($contacts) > 255) {
        return "Контакты должны занимать менее 255 символов";
    }
    return null;
}

/**
 * Функция валидации добавления ставки лота
 * @param string $bet_field Поле в форме для добавления ставки
 * @param int $min_bet Минимальный размер ставки
 * @return string|null Возвращает ошибку валидации или null при отсутствии ошибок
 **/
function validate_bet_add(string $bet_field, int $min_bet): ?string
{
    $empty = validate_filled($bet_field, 'Ваша ставка');
    if ($_POST[$bet_field] !== "0" && $empty) {
        return $empty;
    } elseif (!filter_var($_POST[$bet_field], FILTER_VALIDATE_INT)) {
        return 'Шаг ставки должен быть целым числом больше ноля';
    } elseif ((int)$_POST[$bet_field] < $min_bet) {
        return 'Ваша ставка должна быть не меньше размера минимальной ставки';
    } elseif ((int)$_POST[$bet_field] > 100000000) {
        return 'Ваша ставка должна быть меньше 100 млн. рублей';
    }
    return null;
}

/**
 * Функция проверяет, заполнены ли поля в форме авторизации пользователя
 * @return array Массив, содержащий строки в виде возможных ошибок
 */
function validate_if_filled_in(): array
{
    //массив, где будут храниться ошибки
    $errors = [];
    //обязательные для заполнения поля
    $rules = [
        'email' => function () {
            return validate_filled('email', 'e-mail');
        },
        'password' => function () {
            return validate_filled('password', 'пароль');
        }
    ];
    //Проверяем все поля на заполненность
    foreach ($form = $_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    return array_filter($errors);
}

/**
 * Фукнция проверяет, является ли текущий пользователь неавторизованным
 * @param int|null $user_id ID текущего пользователя
 * @return bool Возвращает true при положительном ответе и false при отрицательном
 */
function is_user_guest(?int $user_id): bool
{
    if ($user_id === null) {
        return true;
    }
    return !isset($user_id);
}

/**
 * Функция сверяет дату и время завершения лота с текущими датой и временем
 * @param array $lot Массив с информацией о лоте, полученной из БД
 * @return bool В случае, если дата завершения в прошлом, возвращает true, иначе false
 */
function is_lot_completed(array $lot): bool
{
    return strtotime($lot['completed_at']) < time();
}


/**
 * Функция проверяет, является ли текущий пользователь автором выбранного лота
 * @param array $lot Массив с информацией о лоте, полученной из БД
 * @param int $user_id ID текущего пользователя
 * @return bool Возвращает true, если это один и тот же пользователь, иначе false
 */
function is_user_author_of_lot(array $lot, int $user_id): bool
{
    return $lot['author_id'] === $user_id;
}

/**
 * Фукнция проверяет, сделал ли данный пользователь последнюю ставку по данному лоту
 * @param array $bets Массив с информацией о последних 10 ставках по лоту
 * @param int $user_id ID текущего пользователя
 * @return bool Возвращает true, если последнюю ставку сделал этот пользователь, иначе false
 */
function is_user_made_last_bet(array $bets, int $user_id): bool
{
    return isset($bets[0]['user_id']) && $bets[0]['user_id'] === $user_id;
}
