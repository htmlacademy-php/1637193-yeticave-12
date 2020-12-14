<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы

$connect = db_connection();
$categories = get_categories_from_db($connect);

if(is_user_guest()) { //проверяем, что пользователь имеет право смотреть ставки
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
}
;

//поиск ставок данного пользователя
$sql_user_bet = 'SELECT item.id as item_id,
                        item.title AS title,
                        category.title AS category,
                        item.image_url,
                        item.completed_at as item_end_time,
                        IFNULL(MAX(bet.total), item.start_price) AS current_price,
                        MAX(bet.created_at) as bet_date,
                        item.winner_id,
                        users.contacts,
                        bet.user_id
                  FROM bet
                   LEFT JOIN item ON item.id = bet.item_id
                   LEFT JOIN users on users.id = item.author_id
                   LEFT JOIN category ON category.id = item.category_id
                  WHERE bet.user_id = ?
                  GROUP BY bet.item_id
                  ORDER BY bet_date DESC';

$sql_user_bet_prepared = db_get_prepare_stmt($connect, $sql_user_bet, [$_SESSION['user']['id']]);
mysqli_stmt_execute($sql_user_bet_prepared);
$sql_result = mysqli_stmt_get_result($sql_user_bet_prepared);

if (!$sql_result) {
    exit('Ошибка запроса: &#129298; ' . mysqli_error($connect));
}
$user_bets = mysqli_fetch_all($sql_result, MYSQLI_ASSOC);

foreach ($user_bets as $bet) {
    if ($bet['winner_id'] == $_SESSION['user']['id']) {
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
