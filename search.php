<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы

$connect = db_connection();
$categories = get_categories_from_db($connect);

$searсh_items = [];

//Добавим полям "название" и "описание" полнотекстовый индекс, чтобы стало возможным выполнять по ним поиск
// mysqli_query($connect, 'CREATE FULLTEXT INDEX gif_ft_search ON gifs(title, description)');

//Получим содержимое поискового запроса. Если поисковый запрос не задан, то присвоим пустую строку
$search = $_GET['search'] ? trim($_GET['search']) : '';

if ($search) { //Будем выполнять поиск лотов, только если был задан поисковый запрос
//Напишем SQL запрос на поиск с использованием директивы MATCH. На месте искомой строки стоит плейсхолдер
//В MATCH указываем – где ищем, то есть указываем, поля, а в AGAINST – что ищем, т.е. поисковый запрос.
    $sql = "SELECT item.id,
                   item.title,
                   item.start_price,
                   item.image_url,
                   IFNULL(MAX(bet.total), item.start_price) AS total,
                   item.created_at,
                   item.completed_at,
                   category.title AS category_title
           FROM item "
        . "INNER JOIN category ON item.category_id = category.id
           LEFT JOIN bet ON bet.item_id = item.id "
        . "WHERE item.completed_at > NOW() AND MATCH(item.title, item.description) AGAINST(?)";

    $stmt = db_get_prepare_stmt($connect, $sql, [$search]); //Подготовка SQL запроса к выполнению
    mysqli_stmt_execute($stmt); //Выполним подготовленное выражение
    $result = mysqli_stmt_get_result($stmt); //получим его результат

    $search_items = mysqli_fetch_all($result, MYSQLI_ASSOC); //и преобразуем в двумерный массив
}

$page_content = include_template('/search.php', ['ad_information' => $search_items]);

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Результаты поиска',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
]);

print($layout_content);
