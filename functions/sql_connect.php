<?php

/**
 * Функция db_connection производит подключение к базе данных "yeticave".
 * Если подключение не выполнено, то происходит вывод ошибки подключения и операции приостанавливаются.
 * @return mysqli Подключение к БД.
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

        $page_content = include_template_error($error, $error_description, $error_link, $error_link_description);

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
 * @return array массив с категориями
 */
function get_categories_from_db($connect)
{
    $sql_category = "SELECT id, title, symbolic_code FROM category";
    $result_category = mysqli_query($connect, $sql_category);

    if (!$result_category) {
        exit('Ошибка запроса: &#129298; ' . mysqli_error($connect));
    }
    return mysqli_fetch_all($result_category, MYSQLI_ASSOC);

}

/**
 * Функция получает массив с самыми новыми, открытыми лотами из базы данных yeticave.
 * Каждый лот включает в себя название, стартовую цену, ссылку на изображение, текущую цену, название категории;
 * @param $connect
 * @return array Массив с самыми новыми, открытыми лотами из базы данных yeticave
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
    return mysqli_fetch_all($result_items, MYSQLI_ASSOC);
}

/**
 * Функция получает массив с информацией о конкретном лоте из базы данных yeticave.
 * Каждый лот включает в себя название, дату создания, описание товара, название категории, ссылку на изображение, дату завершения лота,
 * стартовую цену, шаг ставки, текущую цену, название категории;
 * @param int $item_id - ID Товара
 * @param mysqli $connect - данные о подключении к базе данных
 * @return array Массив с данными о лоте с указанным ID
 */
function get_info_about_lot_from_db($item_id, $connect, $categories)
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
                    IFNULL(MAX(bet.total), item.start_price) AS current_price,
                    item.author_id
              FROM item
                    INNER JOIN category ON category.id = item.category_id
                    INNER JOIN bet on bet.item_id = item.id
              WHERE item.id = ?';

    $sql_lot_prepared = db_get_prepare_stmt($connect, $sql_lot, [$item_id]);
    mysqli_stmt_execute($sql_lot_prepared);
    $sql_result = mysqli_stmt_get_result($sql_lot_prepared);

    $lot_info = mysqli_fetch_array($sql_result, MYSQLI_ASSOC);

    if (!isset($lot_info['id'])) {
        http_response_code(404);
        $error = 'Произошла ошибка: &#129298; ';
        $error_description = 'Страница с id = ' . htmlspecialchars($item_id) . ' не найдена. &#128532; ';
        $error_link = '/index.php';
        $error_link_description = 'Предлагаем вернуться на главную.';

        $page_content = include_template_error($error, $error_description, $error_link, $error_link_description);

        $layout_content = include_template('/layout.php', [
            'content' => $page_content,
            'categories' => $categories,
            'title' => 'Страница c id = .' . $item_id . 'не найдена'
        ]);

        exit($layout_content);
    }
    return $lot_info;
}

/**
 * Фукнция подготавливает запрос в БД о показе последних 10 ставок о конкретном лоте
 * @param int $item_id - ID Товара
 * @param mysqli $connect - данные о подключении к базе данных
 * @return mysqli результат в виде выполненного запроса в БД о показе последних 10 ставок о конкретном лоте
 */
function get_bet_history($item_id, $connect)
{
//история ставок по данному лоту
    $sql_bet = 'SELECT bet.item_id, bet.created_at as date, bet.total, bet.user_id, users.name as username
                FROM bet
                INNER JOIN users ON users.id = bet.user_id
                WHERE item_id = ?
                ORDER BY date DESC
                LIMIT 10';
    $sql_bet_stmt = db_get_prepare_stmt($connect, $sql_bet, [$item_id]);
    mysqli_stmt_execute($sql_bet_stmt);

    return mysqli_stmt_get_result($sql_bet_stmt);
}


/**
 * Фукнция подготавливает запрос в БД о добавлении новой ставки к конкретному лоту
 * @param int $item_id - ID Товара
 * @param mysqli $connect - данные о подключении к базе данных
 * @return mysqli выполненный запрос в БД
 */
function add_bet_in_db($item_id, $connect)
{
    // фильтр входящего значения на соответствие числу, введенного в форму
    $filter_value_bet = filter_input(INPUT_POST, 'cost', FILTER_SANITIZE_NUMBER_INT);
    // создаем шаблон подготовленного выражения для вставки в БД
    $sql_add_bet = 'INSERT INTO bet (total,
                             user_id,
                             item_id)
                    VALUES (?, ?, ?)';

    // формируем подготовленное выражение на основе SQL запроса из $sql_add_bet
    $stmt_add_bet = db_get_prepare_stmt(
        $connect,
        $sql_add_bet,
        [
            $filter_value_bet,
            $_SESSION['user']['id'],
            $item_id
        ]
    );
    //отправка сформированных SQL-выражений в БД
    return mysqli_stmt_execute($stmt_add_bet);
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
    return NULL;
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

/**
 * Функция перенаправляет на главную страницу и заканчивает выполнение скрипта текущей страницы
 */
function redirect_to_main()
{
    header("Location: /index.php");
    return exit();
}

/**
 * Функция выводит подготавливает вывод ошибки для функции include_template в шаблон error_page.php
 * @param string $error Сообщение, что произошла ошибка
 * @param string $error_description Описание ошибки
 * @param string $error_link Ссылка на страницу, куда стоит перейти, чтобы избежать ошибки
 * @param string $error_link_description Описание действия, чтобы избавиться от последствий ошибки
 * @return string вывод ошибки для функции include_template в шаблон error_page.php
 */
function include_template_error($error, $error_description, $error_link, $error_link_description)
{
    return include_template(
        '/error_page.php',
        [
            'error' => $error,
            'error_description' => $error_description,
            'error_link' => $error_link,
            'error_link_description' => $error_link_description
        ]
    );
}

/**
 * Функция проверяет ошибки перед добавлением новой ставки к лоту
 * @param array $lot Массив с информацией о лоте
 * @return array Очищенный от пустых значений массив с возможными ошибками
 */
function check_errors_before_add_bet($lot)
{
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
    return array_filter($errors);
}
