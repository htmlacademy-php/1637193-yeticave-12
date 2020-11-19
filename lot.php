<?php
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/data.php'; //дефолтные переменные
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных

$connect = db_connection();

//if (is_null(filter_input(INPUT_GET, 'id'))) {
//    http_response_code(404);
//    header("Location: pages/404.html");
//    exit("Ошибка подключения: не указан id");
//}

//    if (isset($_GET['id'])) {
//        $id = (int)$_GET['id'];
//    } else {
//        http_response_code(400);
//        exit("Ошибка подключения: не указан id");
//    }
//
//    $sql_lot = 'SELECT  item.id,
//                    item.created_at,
//                    item.title AS title,
//                    item.description,
//                    category.title AS category_title,
//                    item.image_url,
//                    item.completed_at,
//                    item.start_price,
//                    item.bet_step,
//                    IFNULL(MAX(bet.total), item.start_price) AS current_price
//              FROM item
//                    INNER JOIN category ON item.category_id = category.id
//                    LEFT JOIN bet on bet.item_id = item.id
//              WHERE item.id = ' . $id;
//
//    $info_about_lot = mysqli_query($connect, $sql_lot);
//    $lot = mysqli_fetch_array($info_about_lot, MYSQLI_ASSOC);
//
//    if ($lot === NULL) {
//        http_response_code(404);
//        exit("Страница с id =" . $id . " не найдена.");
//    }


$lot = get_info_about_lot_from_db($connect);
$categories = get_categories_from_db($connect);

$page_content = include_template('lot_page.php', compact('categories', 'lot'));

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => htmlspecialchars($lot['title']),
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
