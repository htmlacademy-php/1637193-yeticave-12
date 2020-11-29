<?php
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/data.php'; //дефолтные переменные
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных

$connect = db_connection();
$categories = get_categories_from_db($connect);

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //Проверяем, что форма была отправлена
    $form = $_POST;

    $required = ['email', 'password', 'name', 'message']; //обязательные для заполнения поля
    $errors = []; // массив, где будут храниться ошибки

    //Проверяем все поля на заполненность
    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    //Найдем в таблице users пользователя с переданным email.
    $email = mysqli_real_escape_string($connect, $form['email']); //экранирование спец.символов для использования в SQL-выражении
    $sql = "SELECT *
            FROM users
            WHERE email = '$email'";
    $res = mysqli_query($connect, $sql); //запрос в БД

    // Если пользователь есть в результате запроса из БД,
    // тогда эти данные о нем помещаем в новый массив,
    // иначе данных о пользователе в БД еще нет
    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (!count($errors) and $user) {
        //Проверяем, что сохраненный хеш пароля и введенный пароль из формы совпадают.
        if (password_verify($form['password'], $user['password'])) {
            //если совпадение есть, значит пользователь указал верный пароль.
            // Тогда мы можем открыть для него сессию и записать в неё все данные о пользователе
            $_SESSION['user'] = $user;
        } else { //иначе пароль неверный и мы добавляем сообщение об этом в список ошибок
            $errors['password'] = 'Неверный пароль';
        }
    } else { //Если пользователь не найден, то записываем это как ошибку валидации
        $errors['email'] = 'Такой пользователь не найден';
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
    $page_content = include_template('enter_page.php', [
        'categories' => $categories
    ]);

    if (isset($_SESSION['user'])) {
        header("Location: /index.php");
        exit();
    }
}

$layout_content = include_template('layout.php', [
    'content'    => $page_content,
    'categories' => $categories,
    'title'      => 'Тут можно залогиниться',
    'is_auth' => 0
]);

print($layout_content);
