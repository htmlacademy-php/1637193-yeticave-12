<?php
/**
 * @var array $categories
 * @var array $ad_information
 * @var string $user_name
 * @var int $is_auth
 */

require_once './helpers.php'; //дефолтные функции от создателей курса
//require_once './functions/data.php'; //массивы
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени


$connect = mysqli_connect('localhost', 'root', 'root', 'yeticave');
$categories = [];
$ad_information = [];

if (!$connect) {
    print("Ошибка подключения: " . mysqli_connect_error());
} else {
    mysqli_set_charset($connect, "utf8");

    //получение всех категорий:
    $sql_category = "SELECT title, symbolic_code FROM category";
    $result_category = mysqli_query($connect, $sql_category);

    if ($result_category) {
        $categories = mysqli_fetch_all($result_category, MYSQLI_ASSOC);
    } else {
        print('Ошибка запроса: ' . mysqli_error($connect));
    }
    //получение самых новых, открытых лотов.
    // Каждый лот должен включать название, стартовую цену, ссылку на изображение, текущую цену, название категории;
    $sql_it = "SELECT item.title AS 'title',
       item.start_price AS 'start_price',
       item.image_url AS 'image_url',
       IF(bet.total IS NULL, item.start_price, MAX(bet.total)) AS 'total',
       item.created_at AS 'created_at',
       item.completed_at AS 'completed_at',
       category.title AS 'category_title'
FROM item
         INNER JOIN category ON item.category_id = category.id
         LEFT JOIN bet ON item.id = bet.item_id
WHERE item.completed_at > NOW()
GROUP BY item.id
ORDER BY item.created_at DESC";

    $result_items = mysqli_query($connect, sql_item);

    if ($result_items) {
        $ad_information = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
    } else {
        print('Ошибка запроса: ' . mysqli_error($connect));
    }
}


$page_content = include_template('main.php', [
    'categories' => $categories,
    'ad_information' => $ad_information
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Yeticave - Главная страница by Alexander Galkin',
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);


