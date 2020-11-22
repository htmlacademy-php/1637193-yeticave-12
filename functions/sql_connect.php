<?php

/**
 * Функция db_connection производит подключение к базе данных "yeticave".
 * Если подключение не выполнено, то происходит вывод ошибки подключения и операции приостанавливаются.
 * @return mysqli
 */
function db_connection()
{
    $connect = mysqli_connect('localhost', 'root', 'root', 'yeticave');
    mysqli_set_charset($connect, "utf8");

    if (!$connect) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }
    return $connect;
}

/**
 * Функция получает все категории из базы данных yeticave
 * @param $connect mixed данные о подключении к базе данных yeticave
 * @return array|int массив с категориями
 */
function get_categories_from_db($connect)
{
    $sql_category = "SELECT title, symbolic_code FROM category";
    $result_category = mysqli_query($connect, $sql_category);

    if (!$result_category) {
        exit('Ошибка запроса: ' . mysqli_error($connect));
    }
    $categories = mysqli_fetch_all($result_category, MYSQLI_ASSOC);
    return $categories;

}

/**
 * Функция получает массив с самыми новыми, открытыми лотами из базы данных yeticave.
 * Каждый лот включает в себя название, стартовую цену, ссылку на изображение, текущую цену, название категории;
 * @param $connect
 * @return array|int
 */
function get_ad_information_from_db($connect)
{
    $sql_item = "SELECT item.id,
                        item.title AS 'title',
                       item.start_price AS 'start_price',
                       item.image_url AS 'image_url',
                       IFNULL(MAX(bet.total), item.start_price) AS 'total',
                       item.created_at AS 'created_at',
                       item.completed_at AS 'completed_at',
                       category.title AS 'category_title'
                FROM item
                         INNER JOIN category ON item.category_id = category.id
                         LEFT JOIN bet ON item.id = bet.item_id
                WHERE item.completed_at > NOW()
                GROUP BY item.id
                ORDER BY item.created_at DESC";

    $result_items = mysqli_query($connect, $sql_item);

    if (!$result_items) {
        exit('Ошибка запроса: ' . mysqli_error($connect));
    }
    $ad_information = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
    return $ad_information;
}

/**
 * Функция получает массив с информацией о лотах из базы данных yeticave.
 * Каждый лот включает в себя название, дату создания, описание товара, название категории, ссылку на изображение, дату завершения лота,
 * стартовую цену, шаг ставки, текущую цену, название категории;
 * @param $id - ID Товара
 * @param $connect - данные о подключении к базе данных
 * @return array|int
 */
function get_info_about_lot_from_db($id, $connect)
{
    $sql_lot = 'SELECT item.id,
                    item.created_at,
                    item.title AS title,
                    item.description,
                    category.title AS category_title,
                    item.image_url,
                    item.completed_at,
                    item.start_price,
                    item.bet_step,
                    IFNULL(MAX(bet.total), item.start_price) AS current_price
              FROM item
                    INNER JOIN category ON category.id = item.category_id
                    INNER JOIN bet on bet.item_id = item.id
              WHERE item.id = ' . $id;

    $info_about_lot = mysqli_query($connect, $sql_lot);
    $lot_info = mysqli_fetch_array($info_about_lot, MYSQLI_ASSOC);

    if (!isset($lot_info['id'])) {
        http_response_code(404);
        header("Location: 404.php");
        print("Страница с id = " . $id . " не найдена.");
        die();
    }

    return $lot_info;
}
