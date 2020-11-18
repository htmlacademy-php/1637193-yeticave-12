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
        print("Ошибка подключения: " . mysqli_connect_error());
        die();
    }
    return $connect;
}

/**
 * Функция получает все категории из базы данных yeticave
 * @param $connect данные о подключении к базе данных yeticave
 * @return array|int массив с категориями
 */
function get_categories_from_db($connect)
{
    $sql_category = "SELECT title, symbolic_code FROM category";
    $result_category = mysqli_query($connect, $sql_category);

    if (!$result_category) {
        print('Ошибка запроса: ' . mysqli_error($connect));
        die();
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
        print('Ошибка запроса: ' . mysqli_error($connect));
        die();
    }
    $ad_information = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
    return $ad_information;
}

