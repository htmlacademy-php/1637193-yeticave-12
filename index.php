<?php
/**
 * @var array $categories
 * @var array $ad_information
 * @var string $user_name
 * @var int $is_auth
 */

require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/config.php'; //пользовательские константы и данные по подключению к БД
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных

$connect = db_connection();

$categories = get_categories_from_db($connect);
$ad_information = get_ad_information_from_db($connect);



$page_content = include_template('main.php', compact('categories', 'ad_information'));

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => TITLE_MAIN_PAGE,
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);


