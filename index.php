<?php
/**
 * @var array $categories
 * @var array $ad_information
 * @var string $user_name
 * @var int $is_auth
 */

require_once './helpers.php';
require_once './functions/data.php';
require_once './functions/numbers.php';
require_once './functions/time.php';


$page_content = include_template('main.php', [
    'categories' => $categories,
    'ad_information' => $ad_information
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Yeticave - Главная страница by Alexander Galkin',
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
?>
