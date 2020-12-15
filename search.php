<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы

$connect = db_connection();
$categories = get_categories_from_db($connect);

$search_items = []; //массив с результатами поиска
$pages_count = 1; //число страниц для вывода результатов поиска
$current_page = 1; //номер текущей страницы
$pages = []; //массив с номерами страниц

//Получим содержимое поискового запроса. Если поисковый запрос не задан, то присвоим пустую строку
$search = $_GET['search'] ? trim($_GET['search']) : '';

if (isset($search)) { //Будем выполнять поиск лотов, только если был задан поисковый запрос

//пагинация:
    $sql_result_count = "SELECT COUNT(*) as count
                         FROM item
                         WHERE item.completed_at > NOW() AND MATCH(title, description) AGAINST(?)";

    $result_stmt_count = get_stmt_result($connect, $sql_result_count, [$search]);

    $items_count = mysqli_fetch_assoc($result_stmt_count)['count']; //Узнаем общее число лотов, подходящих по условиям поиска

    $current_page = $_GET['page'] ?? 1; //Получаем текущую страницу.

    $pages_count = ceil($items_count / LIMIT_OF_SEARCH_RESULT); //Считаем кол-во страниц, которые нужны для вывода результата
    $offset = ($current_page - 1) * LIMIT_OF_SEARCH_RESULT; //Считаем смещение

    $pagination = get_pagination($search, $pages_count, $current_page);

//поиск лотов:
    //SQL запрос на поиск с использованием директивы MATCH(поля,где ищем)..AGAINST(поисковый запрос). На месте искомой строки стоит плейсхолдер
    $sql_search = "SELECT item.id,
                   item.title,
                   item.start_price,
                   item.image_url,
                   IFNULL(MAX(bet.total), item.start_price) AS total,
                   item.created_at,
                   item.completed_at,
                   category.title AS category_title
           FROM item
           INNER JOIN category ON item.category_id = category.id
           LEFT JOIN bet ON bet.item_id = item.id
           WHERE item.completed_at > NOW() AND MATCH(item.title, item.description) AGAINST(?)
           GROUP BY item.id
           ORDER BY item.created_at DESC
           LIMIT ?
           OFFSET ?";

    $result_stmt_search = get_stmt_result($connect, $sql_search, [$search, LIMIT_OF_SEARCH_RESULT, $offset]);

    $search_items = mysqli_fetch_all($result_stmt_search, MYSQLI_ASSOC); //и преобразуем в двумерный массив
}

$page_content = include_template('/search_page.php', [
    'ad_information' => $search_items,
    'search' => $search,
    'pagination' => $pagination
]);

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Результаты поиска  по запросу ' . $search,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'search' => $search
]);

print($layout_content);
