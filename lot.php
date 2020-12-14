<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы

$connect = db_connection();

if (isset($_GET['id'])) {
    $item_id = (int)$_GET['id']; //получаем ID текущего лота
} else {
    http_response_code(400);
    $error = 'Произошла ошибка: &#129298; ';
    $error_description = 'Страница не найдена. &#128532; ';
    $error_link = '/index.php';
    $error_link_description = 'Предлагаем вернуться на главную.';

    $page_content = include_template_error($error, $error_description, $error_link, $error_link_description);

    $layout_content = include_template('/layout.php', [
        'content' => $page_content,
        'categories' => [],
        'title' => 'Страница не найдена',
        'user_name' => $user_name,
        'is_auth' => $is_auth
    ]);

    exit($layout_content);
}
$categories = get_categories_from_db($connect); //получаем список категорий из БД
$lot = get_info_about_lot_from_db($item_id, $connect, $categories); //получаем информацию о лотах из БД

$show_bet_add = true; //добавлять новую ставку по-умолчанию можно

$sql_bet_history = get_bet_history($item_id, $connect);

if (mysqli_num_rows($sql_bet_history) > 0) {
    $bets = mysqli_fetch_all($sql_bet_history, MYSQLI_ASSOC);
    //количество ставок по данному лоту
    $count_bet = $bets !== null ? count($bets) : 0;
}
// отправка нового значения ставки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // берем из БД текущий минимальный размер новой возможной ставки
    $min_bet = $lot['bet_step'] + $lot['current_price'];

    //правило для обязательного поля ввода новой ставки
    $rules = [
        'cost' => function () use (&$min_bet) {
            return validate_bet_add('cost', $min_bet);
        }
    ];
    // сбор возможных ошибок валидации
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);

    //если ошибок нет, то добавляем ставку в БД:
    if (empty($errors)) {
        $result_add_bet = add_bet_in_db($item_id, $connect); //отправляем запрос о добавлении новой ставки

        //если не прошло, то выводим ошибку
        if (!$result_add_bet) {
            $errors['cost'] = "Произошла ошибка сохранения в базу. Попробуйте еще раз позже";
        }
        // если удачно добавили ставку, переадресовываем снова на страницу этого лота
        header('Location: /lot.php?id=' . $item_id);
    }
}
//проверка ограничений показа блока добавления ставки
if (is_user_guest() || is_lot_completed($lot) || is_user_author_of_lot($lot) || is_user_made_last_bet($bets)) {
    $show_bet_add = false;
}

$page_content = include_template('/lot_page.php', compact('item_id', 'lot', 'errors', 'bets', 'count_bet', 'show_bet_add'));

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => htmlspecialchars($lot['title']),
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
