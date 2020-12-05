<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключение всех функций и констант в отдельном файле

session_start();
$_SESSION = [];

$connect = db_connection();
$categories = get_categories_from_db($connect);

$error = 'Вы вышли из аккаунта';
$error_description = 'Причем сделали это успешно. &#128521;';
$error_link = '/index.php';
$error_link_description = 'Предлагаем вернуться на главную.';

$page_content = include_template('error_page.php', [
    'error' => $error,
    'error_description' => $error_description,
    'error_link' => $error_link,
    'error_link_description' => $error_link_description
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Вы вышли из аккаунта',
    'user_name' => $user_name,
    'is_auth' => $is_auth,
    'categories' => $categories
]);

exit($layout_content);
