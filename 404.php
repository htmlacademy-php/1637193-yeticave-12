<?php
/**
 * @var array $lot
 * @var string $user_name
 * @var boolean $is_auth
 */
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/config.php'; //пользовательские константы и данные по подключению к БД
require_once './functions/sql_connect.php'; //параметры подключения к базе данных

$connect = db_connection();

$categories = get_categories_from_db($connect);

if (!isset($_GET['id'])) {
    http_response_code(400);
    $error = 'Ошибка подключения:' . mysqli_connect_error();
    $page_content = include_template('404_page.php', [
        'error' => $error,
        'categories' => $categories,
        'lot' => $lot
    ]);
}

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => htmlspecialchars($lot['title']),
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);



