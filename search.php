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

    $stmt_count = db_get_prepare_stmt($connect, $sql_result_count, [$search]); //Подготовка SQL запроса к выполнению
    mysqli_stmt_execute($stmt_count); //Выполним подготовленное выражение
    $result_stmt_count = mysqli_stmt_get_result($stmt_count); //получим его результат

    $items_count = mysqli_fetch_assoc($result_stmt_count)['count']; //Узнаем общее число лотов, подходящих по условиям поиска

    $current_page = $_GET['page'] ?? 1; //Получаем текущую страницу.

    $pages_count = ceil($items_count / LIMIT_OF_SEARCH_RESULT); //Считаем кол-во страниц, которые нужны для вывода результата
    $offset = ($current_page - 1) * LIMIT_OF_SEARCH_RESULT; //Считаем смещение

    $pages = range(1, $pages_count); //Заполняем массив номерами всех страниц

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
           ORDER BY item.created_at DESC LIMIT " . LIMIT_OF_SEARCH_RESULT . " OFFSET " . $offset;

    $stmt_search = db_get_prepare_stmt($connect, $sql_search, [$search]); //Подготовка SQL запроса к выполнению
    mysqli_stmt_execute($stmt_search); //Выполним подготовленное выражение
    $result_stmt_search = mysqli_stmt_get_result($stmt_search); //получим его результат

    $search_items = mysqli_fetch_all($result_stmt_search, MYSQLI_ASSOC); //и преобразуем в двумерный массив
}

$page_content = include_template('/search_page.php', [
    'ad_information' => $search_items,
    'pages_count' => $pages_count,
    'pages' => $pages,
    'search' => $search,
    'current_page' => $current_page
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
