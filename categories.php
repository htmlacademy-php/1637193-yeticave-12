<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы

$connect = db_connection();
$categories = get_categories_from_db($connect);

$category_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT); //получаем ID категории
$category_index = array_search($category_id,
    array_column($categories, 'id')); //получаем индекс элемента массива категорий с указанным ID
//если такого индекса категории нет, выводим ошибку
if (!isset($category_index) || (isset($category_index) && $category_index === '') || $category_index === false) {
    error_output(404);
}

$category_title_output = 'Категории не существует';
$category_title = htmlspecialchars($categories[$category_index]['title']); //получаем название категории по индексу

if (isset($category_title) && $category_title !== '') {
    $category_title_output = 'Все лоты в категории "' . $category_title . '"'; //вывод заголовка для страницы категории
}
//пагинация
$current_page = (int)filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT); //Получаем номер текущей страницы.
if (!$current_page || !isset($current_page)) {
    $current_page = 1;
}

$items_count = get_category_count($connect, $category_id)['count']; //Узнаем общее число лотов, подходящих по условиям запроса в категорию

$pages_count = ceil($items_count / LIMIT_OF_SEARCH_RESULT); //Считаем кол-во страниц, которые нужны для вывода результата
$offset = ($current_page - 1) * LIMIT_OF_SEARCH_RESULT; //Считаем смещение

$search_page = pathinfo($_SERVER['SCRIPT_NAME'])['basename'] ?? 'categories.php';
$pagination = get_pagination($pages_count, $current_page, $search_page, '', $category_id); //подключаем пагинацию

$category_items = get_lot_category_count($connect, $category_id,
    $offset); //массив с информацией о лотах с ограничением вывода на 1 страницу

$page_content = include_template('/categories_page.php', [
    'category_items' => $category_items,
    'category_name' => $category_title_output,
    'pagination' => $pagination
]);

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => $category_title_output,
    'is_auth' => $is_auth,
    'user_name' => $user_name
]);

print($layout_content);
