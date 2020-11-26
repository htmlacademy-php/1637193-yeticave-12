<?php
/**
 * @var string $user_name
 * @var boolean $is_auth
 */
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/data.php'; //дефолтные переменные
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных

$connect = db_connection();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    http_response_code(400);
    exit("Ошибка подключения: не указан id");
}

$lot = get_info_about_lot_from_db($id, $connect);
$categories = get_categories_from_db($connect);

connect_db_error($connect, $categories);

$page_content = include_template('lot_page.php', compact('categories', 'lot'));

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => htmlspecialchars($lot['title']),
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
