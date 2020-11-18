<?php
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/data.php'; //дефолтные переменные
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных

$connect = db_connection();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    http_response_code(404);
    exit("Ошибка подключения: не указан id");
}

$sql_lot = "SELECT  item.created_at,
                    item.title AS title,
                    item.description,
                    category.title AS category_title,
                    item.image_url,
                    item.completed_at,
                    item.start_price,
                    item.bet_step,
                    IFNULL(MAX(bet.total), item.start_price) AS current_price
              FROM item
                    JOIN category ON item.category_id = category.id
              WHERE item.id = '" . $id . "'";

$result_lot = mysqli_query($connect, $sql_lot);
$lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);

if ($lot === NULL) {
    http_response_code(404);
    exit("Страница с id =" . $id . " не найдена.");
}
$sql_read_category = "SELECT * FROM category";
$result_category = mysqli_query($connect, $sql_read_category);

$categories = mysqli_fetch_all($result_category, MYSQLI_ASSOC);

//$categories = get_categories_from_db($connect);


$page_content = include_template('lot_page.php', compact('categories', 'lot'));

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => htmlspecialchars($lot['category_title']),
    'user_name' => $user_name,
    'is_auth' => $is_auth
];

print($layout_content);
