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
if(is_user_guest($user_id)) {
    http_response_code(403);
    $error = 'Ошибка 403';
    $error_description = 'Для просмотра сделанных ставок необходимо пройти авторизацию на сайте.';
    $error_link = '/enter.php';
    $error_link_description = 'Авторизоваться можно по этой ссылке.';

    $page_content = include_template_error($error, $error_description, $error_link, $error_link_description);

    $layout_content = include_template('/layout.php', [
        'content' => $page_content,
        'categories' => $categories,
        'title' => 'Лот добавить пока нельзя',
        'user_name' => $user_name,
        'is_auth' => $is_auth
    ]);
    exit($layout_content);
};
//поиск ставок данного пользователя
$user_bets = search_users_bet($connect, $user_id);

//передаем по ссылке данные о том, является ли юзер победителем и закончился ли уже лот
foreach ($user_bets as &$bet) {
    if ($bet['winner_id'] == $user_id) {
        $bet['winner'] = true;
    }
    $remaining_time = get_date_range($bet['item_end_time']);
    $bet['remaining_time'] = $remaining_time;
    if ($remaining_time[0] == '00' && $remaining_time[1] == '00') {
        $bet['lot_ended'] = true;
    }
}

$page_content = include_template('/my_bets_page.php', [
    'bets' => $user_bets
]);
$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => "Ставки пользователя $user_name",
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
