<?php

/**
 * Функция db_connection производит подключение к базе данных "yeticave".
 * Если подключение не выполнено, то происходит вывод ошибки подключения и операции приостанавливаются.
 * @return mysqli Подключение к БД.
 */
function db_connection(): mysqli
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
 * Функция получает список категорий размещенных лотов из базы данных yeticave
 * @param mysqli $connect данные о подключении к базе данных yeticave
 * @return array массив со списком категорий
 */
function get_categories_from_db(mysqli $connect): array
{
    $sql_category = "SELECT id, title, symbolic_code FROM category";
    $result_category = mysqli_query($connect, $sql_category);

    if (!$result_category) {
        exit('Ошибка запроса: &#129298; ' . mysqli_error($connect));
    }
    return mysqli_fetch_all($result_category, MYSQLI_ASSOC);

}

/**
 * Фукнция считает количество лотов, принадлежащих выбранной категории, для пагинации
 * @param mysqli $connect Данные о подключении к БД
 * @param int $category_id номер данной категории из БД
 * @return array|null Возвращает массив с количеством элементов, равных количеству лотов из выбранной категории, либо NULL, если лоты из категории не найдены
 */
function get_category_count(mysqli $connect, int $category_id)
{
    $sql_lots_count = 'SELECT COUNT(*) as count
                       FROM item
                       WHERE completed_at > NOW() AND category_id = ?';

    $sql_result_lots_count = get_stmt_result($connect, $sql_lots_count, [$category_id]);

    $fetch_array = mysqli_fetch_array($sql_result_lots_count, MYSQLI_ASSOC);

    if ($fetch_array === []) {
        $fetch_array = NULL;
    }
    return $fetch_array;
}

/**
 * Функция получает список лотов, принадлежащих выбранной категории, в виде массива с элементами, содержащими информацию о лотах из выбранной категории
 * @param mysqli $connect Данные о подключении к БД
 * @param int $category_id номер данной категории из БД
 * @param int $offset Смещение выборки количества запросов на 1 странице, т.е. начиная с какой записи будут возвращены ограничения по выборке
 * @return array|null Возвращает массив с элементами, содержащими информацию о лотах из выбранной категории, либо NULL, если лоты из категории не найдены
 */
function get_lot_category_count($connect, int $category_id, int $offset)
{
    $sql_lots = 'SELECT item.id,
                        completed_at,
                        category.title AS category,
                        item.category_id AS category_id,
                        item.title AS item_title,
                        image_url,
                        IFNULL(MAX(bet.total), item.start_price) AS total,
                        (SELECT COUNT(*)
                         FROM bet
                         WHERE bet.item_id = item.id) AS count_bet
                FROM item
                JOIN category ON item.category_id = category.id
                LEFT JOIN bet on item.id = bet.item_id
                WHERE completed_at > NOW() AND category.id = ?
                GROUP BY item.id
                ORDER BY item.created_at DESC
                LIMIT ?
                OFFSET ?';

    $result_lots = get_stmt_result($connect, $sql_lots, [$category_id, LIMIT_OF_SEARCH_RESULT, $offset]);

    $fetch_lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

    if ($fetch_lots === []) {
        $fetch_lots = null;
    }
    return $fetch_lots;
}

/**
 * Функция получает массив с самыми новыми, открытыми лотами из базы данных yeticave.
 * Каждый лот включает в себя название, стартовую цену, ссылку на изображение, текущую цену, название категории;
 * @param mysqli $connect данные о подключении к базе данных
 * @return array Массив с самыми новыми, открытыми лотами из базы данных yeticave
 */
function get_ad_information_from_db($connect): array
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
 * Фукнция получает массив с самыми новыми, открытыми лотами из базы данных yeticave с ограничением числа лотов на 1 странице
 * @param mysqli $connect Данные о подключении к БД
 * @param int $offset Смещение выборки количества запросов на 1 странице, т.е. начиная с какой записи будут возвращены ограничения по выборке
 * @return array Ассоциативный массив с данными о лотах для показа на 1 странице
 */
function get_pagination_info_about_items(mysqli $connect, int $offset): array
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
                ORDER BY item.created_at DESC
                LIMIT ?
                OFFSET ?";
    $result_items = get_stmt_result($connect, $sql_item, [LIMIT_OF_SEARCH_RESULT, $offset]);

    return mysqli_fetch_all($result_items, MYSQLI_ASSOC);
}

/**
 * Функция получает массив с информацией о конкретном лоте из базы данных yeticave.
 * Каждый лот включает в себя название, дату создания, описание товара, название категории, ссылку на изображение, дату завершения лота,
 * стартовую цену, шаг ставки, текущую цену, название категории;
 * @param int $item_id - ID Товара
 * @param mysqli $connect - данные о подключении к базе данных
 * @param array $categories - массив со списком категорий размещенных лотов
 * @return array Массив с данными о лоте с указанным ID
 */
function get_info_about_lot_from_db(int $item_id, mysqli $connect, array $categories): array
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
 * @return mysqli_result|false результат в виде выполненного запроса в БД о показе последних 10 ставок о конкретном лоте, иначе false
 */
function get_bet_history(int $item_id, mysqli $connect): mysqli_result
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
 * @param int $user_id - ID пользователя, который добавляет ставку
 * @return bool true в случае выполненного запроса в БД, иначе false
 */
function add_bet_in_db(int $item_id, mysqli $connect, int $user_id): bool
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
            $user_id,
            $item_id
        ]
    );
    //проверка отправки сформированных SQL-выражений в БД
    return mysqli_stmt_execute($stmt_add_bet);
}

/**
 * Вспомогательная функция для получения значений из POST-запроса
 * @param string $name поле, из которого будет браться значение POST
 * @return string содержимое POST-запроса
 */
function get_post_value(string $name): ?string
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
function show_add_lot_page(string $user_name, array $categories, array $errors = []): void
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
function include_template_error($error, $error_description, $error_link, $error_link_description): ?string
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
function check_errors_before_add_bet($lot): array
{
    // берем из БД текущий минимальный размер новой возможной ставки
    $min_bet = $lot['bet_step'] + $lot['current_price'];

    //правило для обязательного поля ввода новой ставки
    $rules = [
        'cost' => function () use ($min_bet) {
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

/**
 * Функция осуществляет поиск ставок указанного пользователя в БД
 * @param mysqli $connect данные о подключении к базе данных
 * @param int $user_id ID текущего пользователя
 * @return array Ассоциативный массив с данными о ставках пользователя
 */
function search_users_bet($connect, int $user_id): array
{
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

$sql_user_bet_prepared = db_get_prepare_stmt($connect, $sql_user_bet, [$user_id]);
mysqli_stmt_execute($sql_user_bet_prepared);
$sql_result = mysqli_stmt_get_result($sql_user_bet_prepared);

if (!$sql_result) {
    exit('Ошибка запроса: &#129298; ' . mysqli_error($connect));
}
return mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
}
