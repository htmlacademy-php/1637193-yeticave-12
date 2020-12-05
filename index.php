<?php
/**
 * @var array $categories
 * @var array $ad_information
 * @var string $user_name
 * @var string $is_auth
 */

session_start();
require_once './functions/bootstrap.php'; //подключение всех функций и констант в отдельном файле

$connect = db_connection();
$categories = get_categories_from_db($connect);
$ad_information = get_ad_information_from_db($connect);

$page_content = include_template('/main.php', compact('categories', 'ad_information'));

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Yeticave - Главная страница by Alexander Galkin',
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);


