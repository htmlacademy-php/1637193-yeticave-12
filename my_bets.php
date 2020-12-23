<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы

$connect = db_connection();
$categories = get_categories_from_db($connect);

$user_id = $_SESSION['user']['id'] ?? null; //проверяем, авторизован ли пользователь

//проверяем, что пользователь имеет право смотреть ставки
if (is_user_guest($user_id)) {
    error_output(403, 'my_bets');
};
//поиск ставок данного пользователя
$user_bets = search_users_bet($connect, $user_id);

$user_bets_count = count($user_bets); //Узнаем общее число ставок, подходящих по условиям поиска
$pagination = null; //пагинация

//если число ставок пользователя больше 9, то нужно подключение пагинации:
if ($user_bets_count > LIMIT_OF_SEARCH_RESULT) {
    $current_page = (int)filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT); //Получаем номер текущей страницы.
    if (!$current_page || !isset($current_page)) {
        $current_page = 1;
    }

    $pages_count = ceil($user_bets_count / LIMIT_OF_SEARCH_RESULT); //Считаем кол-во страниц, которые нужны для вывода результата
    $offset = ($current_page - 1) * LIMIT_OF_SEARCH_RESULT; //Считаем смещение

    $search_page = pathinfo($_SERVER['SCRIPT_NAME'])['basename'] ?? 'my_bets.php';

    $pagination = get_pagination($pages_count, $current_page, $search_page); //подключаем пагинацию

    $user_bets = search_users_bet_about_items($connect, $user_id,
        $offset); //массив с информацией о лотах с ограничением  вывода на 1 страницу
}

//передаем по ссылке данные о том, является ли юзер победителем
foreach ($user_bets as &$bet) {
    if ($bet['winner_id'] === $user_id) {
        $bet['winner'] = true;
    }
}

$page_content = include_template('/my_bets_page.php', [
    'bets' => $user_bets,
    'pagination' => $pagination
]);
$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => "Ставки пользователя $user_name",
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
