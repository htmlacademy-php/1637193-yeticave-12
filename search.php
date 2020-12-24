<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы

$connect = db_connection();
$categories = get_categories_from_db($connect);

$search_items = []; //массив с результатами поиска
$pagination = null; //пагинация

//Получим содержимое поискового запроса. Если поисковый запрос не задан, то присвоим пустую строку
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
if (!$search || !isset($search)) {
    $search = '';
} else {
    $search = trim($search);
}

if (isset($search) && $search !== '') { //Будем выполнять поиск лотов, только если был задан поисковый запрос
    //пагинация:
    $items_count = get_search_items_count($connect, $search);  //Узнаем общее число лотов, подходящих по условиям поиска

    $current_page = (int)filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT); //Получаем номер текущей страницы.
    if (!$current_page || !isset($current_page)) {
        $current_page = 1;
    }

    $pages_count = ceil($items_count / LIMIT_OF_SEARCH_RESULT); //Считаем кол-во страниц, которые нужны для вывода результата
    $offset = ($current_page - 1) * LIMIT_OF_SEARCH_RESULT; //Считаем смещение

    $search_page = pathinfo($_SERVER['SCRIPT_NAME'])['basename'] ?? 'search.php';
    $pagination = get_pagination($pages_count, $current_page, $search_page, $search);

    //поиск лотов:
    $search_items = get_search_items($connect, $search, $offset);
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
