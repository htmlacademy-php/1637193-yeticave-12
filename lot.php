<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/config.php'; //пользовательские константы и данные по подключению к БД
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных

session_start();
$connect = db_connection();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    http_response_code(400);
    $error = 'Произошла ошибка: &#129298; ';
    $error_description = 'Страница не найдена. &#128532; ';
    $error_link = '/index.php';
    $error_link_description = 'Предлагаем вернуться на главную.';
    $page_content = include_template(
        '/error_page.php',
        [
            'error' => $error,
            'error_description' => $error_description,
            'error_link' => $error_link,
            'error_link_description' => $error_link_description
        ]
    );
    $layout_content = include_template('/layout.php', [
        'content' => $page_content,
        'categories' => [],
        'title' => 'Страница не найдена',
        'user_name' => '',
        'is_auth' => 0
    ]);

    exit($layout_content);
}
$categories = get_categories_from_db($connect);
$lot = get_info_about_lot_from_db($id, $connect, $categories);

$page_content = include_template('/lot_page.php', compact('categories', 'lot'));

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => htmlspecialchars($lot['title']),
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
