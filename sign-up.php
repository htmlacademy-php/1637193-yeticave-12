<?php
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/config.php'; //пользовательские константы и данные по подключению к БД
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных
require_once './functions/check.php'; //функции, проверяющие введенные в форму данные на корректность

$connect = db_connection();
$categories = get_categories_from_db($connect);

$tpl_data = []; // временный массив для записи данных нового пользователя для вывода данных в случае ошибок


if ($_SERVER['REQUEST_METHOD'] == 'POST') { //Проверяем, что форма была отправлена
    $errors = []; // массив, где будут храниться ошибки

    $required = ['email', 'password', 'name', 'message']; //обязательные для заполнения поля

    $rules = [
        'email' => function () {
            return validate_email($_POST['email']);
        },
        'password' => function () {
            return validate_password($_POST['password']);
        },
        'name' => function () {
            return validate_filled('name', 'имя пользователя');
        },
        'message' => function () {
            return validate_contacts($_POST['message']);
        }
    ];

    //Проверяем все поля на заполненность
    foreach ($form = $_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    //проверка существования пользователя с email из формы
    if (empty($errors['email'])) {
        $errors['email'] = validate_unique_email($connect);
    }
    $errors = array_filter($errors);

    //при отсутствии ошибок добавим нового пользователя в БД
    if (empty($errors)) {

        //Чтобы не хранить пароль в открытом виде преобразуем его в хеш
        $password_hash = password_hash($form['password'], PASSWORD_DEFAULT);

        $sql_new_user = 'INSERT INTO users (email, name, password, contacts) VALUES (?, ?, ?, ?)';
        $prepared_sql = db_get_prepare_stmt($connect, $sql_new_user, [strtolower($form['email']), $form['name'], $password_hash, $form['message']]); //подготовка SQL-запроса к выполнению
        $result_sql = mysqli_stmt_execute($prepared_sql); //выполняет подготовленный запрос

        //Редирект на страницу входа, если пользователь был успешно добавлен в БД.
        if ($result_sql && empty($errors)) {
            header("Location: /enter.php");
            exit();
        }
    }
    //Передаем в шаблон вывода ошибок сам список ошибок и сохраненные данные из формы
    $tpl_data['errors'] = $errors;
    $tpl_data['values'] = $form;
}
//подключение лейаут, вывод его на экран
$page_content = include_template('/registration_page.php', $tpl_data);

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Регистрация в Yeticave',
    'is_auth' => 0
]);

print($layout_content);
