<?php
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/config.php'; //пользовательские константы и данные по подключению к БД
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных
require_once './functions/check.php'; //функции, проверяющие введенные в форму данные на корректность

$connect = db_connection();
session_start();
$categories = get_categories_from_db($connect);

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //Проверяем, что форма была отправлена
    $form = $_POST;

    $required = ['email', 'password']; //обязательные для заполнения поля
    $errors = []; // массив, где будут храниться ошибки

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

    //Проверим, есть ли в таблице users пользователь с переданным в форме email.
    $check_result_sql = check_unique_email($connect);

    // Если в результате проверки пользователь есть в результате запроса из БД,
    // тогда эти данные о нем получаем в виде нового ассоциативного массива,
    // иначе данных о пользователе в БД еще нет
    $user = $check_result_sql ? mysqli_fetch_array($check_result_sql, MYSQLI_ASSOC) : null;
    //check_user_db ($check_result_sql, $errors, $form);

    $errors = array_filter($errors);

    if (!count($errors) and $user) {
        //Проверяем, что сохраненный хеш пароля и введенный пароль из формы совпадают.
        if (password_verify($_POST['password'], $user['password'])) {
            //если совпадение есть, значит пользователь указал верный пароль.
            // Тогда мы можем открыть для него сессию и записать в неё все данные о пользователе
            $_SESSION['user'] = $user;
        } else { //иначе пароль неверный и мы добавляем сообщение об этом в список ошибок
            $errors['password'] = 'Неверный пароль: проверьте введенные символы на корректность';
        }
    } elseif (empty($errors['email'])) { //Если пользователь не найден, то записываем это как ошибку валидации
        $errors['email'] = 'Пользователь с указанным e-mail не зарегистрирован на сайте: проверьте правильность введенного e-mail';
    }

    //Если были ошибки, значит мы снова должны показать форму входа, передав в шаблон список полученных ошибок
    if (count($errors)) {
        $page_content = include_template('enter_page.php', ['form' => $form, 'errors' => $errors]);
    } else { //ошибок нет, значит аутентификация успешна: возвращаем пользователя на главную
        header("Location: /index.php");
        exit();
    }
} else { //если форма не отправлена, то проверяем наличие сессии у пользователя:
    //сессия есть: пользователь залогинен и ему нужно показать главную
    //сессии нет: показываем форму для захода на сайт
    $page_content = include_template('/enter_page.php', [
        'categories' => $categories
    ]);

    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
}

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Тут можно залогиниться',
    'is_auth' => 0
]);

print($layout_content);
