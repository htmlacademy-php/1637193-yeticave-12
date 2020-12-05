<?php

/**
 * Функция db_connection производит подключение к базе данных "yeticave".
 * Если подключение не выполнено, то происходит вывод ошибки подключения и операции приостанавливаются.
 * @return mysqli
 */
function db_connection()
{
    $connect = mysqli_connect(DB_CONNECTION_DATA['host'], DB_CONNECTION_DATA['user'], DB_CONNECTION_DATA['password'], DB_CONNECTION_DATA['database']);
    mysqli_set_charset($connect, "utf8");

    if (!$connect) {
        $error = 'Произошла ошибка подключения: &#129298; ';
        $error_description = 'У нас произошла техническая ошибка. &#128532; ';
        $error_link = '/index.php';
        $error_link_description = 'Возвращайтесь к нам немного позже.';
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
            'title' => 'Возвращайтесь чуть позже',
            'user_name' => '',
            'is_auth' => 0
        ]);

        exit($layout_content);
    }
    return $connect;
}

/**
 * Функция получает все категории из базы данных yeticave
 * @param $connect mixed данные о подключении к базе данных yeticave
 * @return array|int  массив с категориями
 */
function get_categories_from_db($connect)
{
    $sql_category = "SELECT id, title, symbolic_code FROM category";
    $result_category = mysqli_query($connect, $sql_category);

    if (!$result_category) {
        exit('Ошибка запроса: &#129298; ' . mysqli_error($connect));
    }
    $categories = mysqli_fetch_all($result_category, MYSQLI_ASSOC);
    return $categories;

}

/**
 * Функция получает массив с самыми новыми, открытыми лотами из базы данных yeticave.
 * Каждый лот включает в себя название, стартовую цену, ссылку на изображение, текущую цену, название категории;
 * @param $connect
 * @return array|int
 */
function get_ad_information_from_db($connect)
{
    $sql_item = "SELECT item.id,
                        item.title AS 'title',
                       item.start_price AS 'start_price',
                       item.image_url AS 'image_url',
                       IFNULL(MAX(bet.total), item.start_price) AS 'total',
                       item.created_at AS 'created_at',
                       item.completed_at AS 'completed_at',
                       category.title AS 'category_title'
                FROM item
                         INNER JOIN category ON item.category_id = category.id
                         LEFT JOIN bet ON item.id = bet.item_id
                WHERE item.completed_at > NOW()
                GROUP BY item.id
                ORDER BY item.created_at DESC";

    $result_items = mysqli_query($connect, $sql_item);

    if (!$result_items) {
        exit('Ошибка запроса: &#129298; ' . mysqli_error($connect));
    }
    $ad_information = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
    return $ad_information;
}

/**
 * Функция получает массив с информацией о лотах из базы данных yeticave.
 * Каждый лот включает в себя название, дату создания, описание товара, название категории, ссылку на изображение, дату завершения лота,
 * стартовую цену, шаг ставки, текущую цену, название категории;
 * @param $id - ID Товара
 * @param $connect - данные о подключении к базе данных
 * @return array|int
 */
function get_info_about_lot_from_db($id, $connect, $categories)
{
    $sql_lot = 'SELECT item.id,
                    item.created_at,
                    item.title AS title,
                    item.description,
                    category.title AS category_title,
                    item.image_url,
                    item.completed_at,
                    item.start_price,
                    item.bet_step,
                    IFNULL(MAX(bet.total), item.start_price) AS current_price
              FROM item
                    INNER JOIN category ON category.id = item.category_id
                    INNER JOIN bet on bet.item_id = item.id
              WHERE item.id = ' . htmlspecialchars($id);

    $info_about_lot = mysqli_query($connect, $sql_lot);
    $lot_info = mysqli_fetch_array($info_about_lot, MYSQLI_ASSOC);

    if (!isset($lot_info['id'])) {
        http_response_code(404);
        $error = 'Произошла ошибка: &#129298; ';
        $error_description = 'Страница с id = ' . htmlspecialchars($id) . ' не найдена. &#128532; ';
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
            'categories' => $categories,
            'title' => 'Страница c id = .' . $id . 'не найдена'
        ]);

        exit($layout_content);
    }

    return $lot_info;
}

/**
 * Вспомогательная функция для получения значений из POST-запроса
 * @param $name mixed поле, из которого будет браться значение POST
 * @return mixed|string содержимое POST-запроса
 */
function get_post_value($name)
{
    return $_POST[$name] ?? "";
}

/**
 * Сохраняет файл в папку /uploads/, не изменяя имени файла.
 * @param string $field_name Имя поля файла
 * @return string|null Возвращает url сохраненного файла или возвращает NULL в случае ошибки
 **/
function save_file(string $field_name): ?string
{
    if (isset($_FILES[$field_name])) {
        $prefix = uniqid();
        $file_name = $prefix . '_' . $_FILES[$field_name]['name'];
        $file_path = $_SERVER['DOCUMENT_ROOT'] . _DS . NAME_FOLDER_UPLOADS_FILE . _DS;
        $file_url = _DS . NAME_FOLDER_UPLOADS_FILE . _DS . $file_name;

        if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $file_path . $file_name)) {
            return $file_url;
        }
    }
    return null;
}


/**
 * Функция проверяет значение категории в отправленной форме, и выводит страницу для создания нового лота.
 * @param string $user_name Имя пользователя
 * @param array $categories Массив с значениями категорий лотов
 * @param array $errors Массив для записи возможных ошибок
 */
function show_add_lot_page(string $user_name, $categories, $errors = []): void
{
    $selected_category = $_POST['category'] ?? 0;
    $page_content = include_template('/add_lot.php', compact('categories', 'errors', 'selected_category'));

    $layout_content = include_template(
        '/layout.php',
        [
            'content' => $page_content,
            'categories' => $categories,
            'title' => 'Добавление нового лота',
            'user_name' => $user_name,
            'is_auth' => 1
        ]
    );

    print($layout_content);
}
