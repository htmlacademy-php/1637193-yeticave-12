<?php
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/data.php'; //дефолтные переменные
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных

$connect = db_connection();

//$lot_id = filter_input(INPUT_GET, 'id');

//if (is_null($lot_id)) {
//
//
//}
//$tab = $_GET['tab'] ?? 'popular';

$sql_lot = "SELECT bet_step,
                    completed_at,
                    description,
                    category.title AS category_title,
                    title,
                    image_url,
                    start_price,
                    IF(bet.total IS NULL, item.start_price, MAX(bet.total)) AS current_price
              FROM item
                    JOIN category ON item.category_id = category.id
                    LEFT JOIN bet ON bet.item_id = item.id
              WHERE item.id = '.$id.'
              GROUP BY item.id";

$result_lot = mysqli_query($connect, $sql_lot);

if(!$result_lot) {
    $error = mysqli_error($connect);
    print 'Ошибка MySQL: '.$error;
}

if(!mysqli_num_rows($result_lot)) {
    header("Location: pages/404.html");
};

$lot = mysqli_fetch_all($result_lot, MYSQLI_ASSOC);

$categories = get_categories($connect);


$page_content = include_template('lot_page.php', compact('categories', 'lot'));

$layout_content = include_template('layout.php', ['content' => $page_content, 'categories' => $categories, 'title' => $user_name]);

print($layout_content);
