<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
session_start();
require_once './functions/bootstrap.php'; //подключение всех функций и констант в отдельном файле

$connect = db_connection();
$categories = get_categories_from_db($connect);

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //Проверяем, что форма была отправлена
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
    $errors = array_filter($errors);

    //Проверим, есть ли в таблице users пользователь с переданным в форме email.
    $check_result_sql = verify_existence_email_db($connect);

    // Если в результате проверки пользователь есть в результате запроса из БД,
    // тогда эти данные о нем получаем в виде нового ассоциативного массива,
    // иначе данных о пользователе в БД еще нет
    $user = $check_result_sql ? mysqli_fetch_array($check_result_sql, MYSQLI_ASSOC) : null;

    if (!count($errors) and $user) {
        //Проверяем, что сохраненный хеш пароля и введенный пароль из формы совпадают.
        if (password_verify($form['password'], $user['password'])) {
            //если совпадение есть, значит пользователь указал верный пароль.
            // Тогда мы можем открыть для него сессию и записать в неё все данные о пользователе
            $_SESSION['user'] = $user;
        }
    } else { //Если пользователь не найден, то записываем это как ошибку валидации
        $errors['email'] = 'Пользователь с указанными email и паролем не найден. Проверьте введенные данные и попробуйте еще раз';
    }

    //Если были ошибки, значит мы снова должны показать форму входа, передав в шаблон список полученных ошибок
    if (count($errors)) {
        $page_content = include_template('/enter_page.php', ['form' => $form, 'errors' => $errors]);
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
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
