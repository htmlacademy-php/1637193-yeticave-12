<?php

/**
 * Функция db_connection производит подключение к базе данных "yeticave".
 * Если подключение не выполнено, то происходит вывод ошибки подключения и операции приостанавливаются.
 * @return mysqli Подключение к БД либо вывод ошибки подключения.
 */
function db_connection(): mysqli
{
    $connect = mysqli_connect(DB_CONNECTION_DATA['host'], DB_CONNECTION_DATA['user'], DB_CONNECTION_DATA['password'],
        DB_CONNECTION_DATA['database']);
    if (!$connect) {
        exit(error_output(500));
    }
    mysqli_set_charset($connect, "utf8");

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
        exit(error_output(500));
    }
    return mysqli_fetch_all($result_category, MYSQLI_ASSOC);

}

/**
 * Фукнция считает количество лотов, принадлежащих выбранной категории, для пагинации
 * @param mysqli $connect Данные о подключении к БД
 * @param int $category_id номер данной категории из БД
 * @return array|null Возвращает массив с количеством элементов, равных количеству лотов из выбранной категории, либо NULL, если лоты из категории не найдены
 */
function get_category_count(mysqli $connect, int $category_id): ?array
{
    $sql_lots_count = 'SELECT COUNT(*) as count
                       FROM item
                       WHERE completed_at > NOW() AND category_id = ?';

    $sql_result_lots_count = get_stmt_result($connect, $sql_lots_count, [$category_id]);

    $fetch_array = mysqli_fetch_array($sql_result_lots_count, MYSQLI_ASSOC);

    if ($fetch_array === []) {
        $fetch_array = null;
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
function get_lot_category_count(mysqli $connect, int $category_id, int $offset): ?array
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
function get_ad_information_from_db(mysqli $connect): array
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
        exit(error_output(500));
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

    $sql_lot_prepared = get_stmt_result($connect, $sql_lot, [$item_id]);

    $lot_info = mysqli_fetch_array($sql_lot_prepared, MYSQLI_ASSOC);

    if (!isset($lot_info['id'])) {
        exit(error_output(404));
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

    return get_stmt_result($connect, $sql_bet, [$item_id]);
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
 * Сохраняет файл в папку /uploads/, добавляя префикс к началу имени файла.
 * @param string $file Поле в форме для выбора и загрузки файла с ПК пользователя
 * @return string|null Возвращает url сохраненного файла или возвращает NULL в случае ошибки
 **/
function save_file(string $file): ?string
{
    if (isset($_FILES[$file])) {
        $prefix = uniqid();
        $file_name = $prefix . '_' . $_FILES[$file]['name'];
        $file_path = $_SERVER['DOCUMENT_ROOT'] . _DS . NAME_FOLDER_UPLOADS_FILE . _DS;
        $file_url = _DS . NAME_FOLDER_UPLOADS_FILE . _DS . $file_name;

        if (move_uploaded_file($_FILES[$file]['tmp_name'], $file_path . $file_name)) {
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
 * @param int $http_code Код состояния HTTP
 * @return string вывод ошибки для функции include_template в шаблон error_page.php
 */
function include_template_error(
    string $error,
    string $error_description,
    string $error_link,
    string $error_link_description,
    int $http_code = 0
): ?string {
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
        'title' => 'Ошибка ' . $http_code
    ]);

    exit($layout_content);
}

/**
 * Функция проверяет ошибки перед добавлением новой ставки к лоту
 * @param array $lot Массив с информацией о лоте
 * @return array Очищенный от пустых значений массив с возможными ошибками
 */
function check_errors_before_add_bet(array $lot): array
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
function search_users_bet(mysqli $connect, int $user_id): array
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

    $sql_result = get_stmt_result($connect, $sql_user_bet, [$user_id]);

    return mysqli_fetch_all($sql_result, MYSQLI_ASSOC);
}

/**
 * Функция осуществляет поиск ставок указанного пользователя в БД с ограничением числа лотов на 1 странице
 * @param mysqli $connect Данные о подключении к БД
 * @param int $user_id ID текущего пользователя
 * @param int $offset Смещение выборки количества запросов на 1 странице, т.е. начиная с какой записи будут возвращены ограничения по выборке
 * @return array Ассоциативный массив с данными о ставках пользователя для показа на 1 странице
 */
function search_users_bet_about_items(mysqli $connect, int $user_id, int $offset): array
{
    $sql_bet = "SELECT item.id as item_id,
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
                  ORDER BY bet_date DESC
                   LIMIT ?
                   OFFSET ?";
    $result_bet = get_stmt_result($connect, $sql_bet, [$user_id, LIMIT_OF_SEARCH_RESULT, $offset]);

    return mysqli_fetch_all($result_bet, MYSQLI_ASSOC);
}

/**
 * Функция выполняет выражение на основе подготовленного SQL-запроса и возвращает его результат
 * @param mysqli $connect Данные о подключении к БД
 * @param string $sql_result_count SQL-запрос в БД
 * @param array $array_stmt Данные для вставки на место плейсхолдеров
 * @return mysqli_result Результат подготовленного выражения
 */
function get_stmt_result(mysqli $connect, string $sql_result_count, $array_stmt = []): mysqli_result
{
    $stmt = db_get_prepare_stmt($connect, $sql_result_count, $array_stmt); //Подготовка SQL запроса к выполнению
    mysqli_stmt_execute($stmt); //Выполним подготовленное выражение
    $result_stmt = mysqli_stmt_get_result($stmt); //получим его результат

    if (!$result_stmt) {
        exit(error_output(500));
    }
    return $result_stmt;
}

/**
 * Функция считает число лотов, подходящих под результаты поискового запроса
 * @param mysqli $connect Данные о подключении к БД
 * @param string $search Содержимое поискового запрос от пользователя
 * @return int Число лотов, подходящих по условиям поиска
 */
function get_search_items_count(mysqli $connect, string $search): int
{
    $sql_result_count = "SELECT COUNT(*) as count
                         FROM item
                         WHERE item.completed_at > NOW() AND MATCH(title, description) AGAINST(?)";

    $result_stmt_count = get_stmt_result($connect, $sql_result_count, [$search]);

    return mysqli_fetch_assoc($result_stmt_count)['count'];
}

/**
 * Функция возвращает массив с результатами поискового запроса
 * @param mysqli $connect Данные о подключении к БД
 * @param string $search Содержимое поискового запрос от пользователя
 * @param int $offset Смещение выдачи результатов поиска для пагинации
 * @return array Массив с элементами в виде результатов поискового запроса
 */
function get_search_items(mysqli $connect, string $search, int $offset): array
{
    //SQL запрос на поиск с использованием директивы MATCH(поля,где ищем)..AGAINST(поисковый запрос). На месте искомой строки стоит плейсхолдер
    $sql_search = "SELECT item.id,
                   item.title,
                   item.start_price,
                   item.image_url,
                   IFNULL(MAX(bet.total), item.start_price) AS total,
                   item.created_at,
                   item.completed_at,
                   category.title AS category_title
           FROM item
           INNER JOIN category ON item.category_id = category.id
           LEFT JOIN bet ON bet.item_id = item.id
           WHERE item.completed_at > NOW() AND MATCH(item.title, item.description) AGAINST(?)
           GROUP BY item.id
           ORDER BY item.created_at DESC
           LIMIT ?
           OFFSET ?";

    $result_stmt_search = get_stmt_result($connect, $sql_search, [$search, LIMIT_OF_SEARCH_RESULT, $offset]);
    // преобразуем результаты поиска в массив
    return mysqli_fetch_all($result_stmt_search, MYSQLI_ASSOC);
}

/**
 * Функция выдает список последних ставок по лотам без победителей,
 * дата истечения которых меньше или равна текущей
 * @param mysqli $connect данные о подключении к базе данных
 * @return mysqli_result Результат запроса в БД
 */
function get_finished_lots(mysqli $connect): mysqli_result
{
    $sql_finished_lots = 'SELECT bet.id AS bet_id, total, bet.user_id AS user_id, item_id, bet.created_at AS created_at, i.winner_id AS winner_id,
               i.title AS item_title, i.completed_at AS completed_at, u.name AS user_name, u.email AS user_email
            FROM bet
            INNER JOIN item i on bet.item_id = i.id
            INNER JOIN users u on bet.user_id = u.id
            INNER JOIN (
                    SELECT item_id AS item_id_bet, MAX(total) AS max_bet
                    FROM bet
                    GROUP BY item_id
            ) bet_new ON bet.item_id = bet_new.item_id_bet AND bet.total = bet_new.max_bet
            WHERE i.winner_id IS NULL AND i.completed_at <= CURDATE()
            ORDER BY bet_id';


    $result_finished_lots = mysqli_query($connect, $sql_finished_lots);

    if (!$result_finished_lots) {
        exit(error_output(500));
    }
    return $result_finished_lots;
}


/**
 * Функция отправляет сообщение о выигрыше ставки победителю лота
 * @param array $winner Массив с данными победителя лота
 * @return string Отправленное сообщение
 */
function send_email_to_winner(array $winner)
{
    // конфигурация транспорта для доступа к SMTP-серверу
    $transport = (new Swift_SmtpTransport(SMTP_CONFIG['host'], SMTP_CONFIG['port'], SMTP_CONFIG['encryption']))
        ->setUsername(SMTP_CONFIG['username'])
        ->setPassword(SMTP_CONFIG['password']);

    //данные для вставки в шаблон:
    $user_name = $winner['user_name'];
    $item_title = $winner['item_title'];
    $item_id = (int)$winner['item_id'];
    $host_url = $_SERVER['SERVER_NAME'];

    //Передадим список победителей в шаблон, используемый для отправки email
    $message_content = include_template(
        '/email.php',
        [
            'user_name' => $user_name,
            'item_title' => $item_title,
            'item_link' => '/lot.php?id=' . $item_id,
            'host_url' => 'http://' . $host_url
        ]
    );

    //Установим параметры сообщения: тема, отправитель и список его получателей
    $message = (new Swift_Message())
        ->setSubject("Ваша ставка победила")
        ->setFrom(['keks@phpdemo.ru' => 'yeticave'])
        ->setTo([$winner['user_email'] => $winner['user_name']])
        ->setBody($message_content, 'text/html');

    //Передадим в главный объект библиотеки SwiftMailer, ответственный за отправку сообщений, созданный объект с SMTP-сервером.
    // и отправим сообщение
    return (new Swift_Mailer($transport))->send($message);
}

/**
 * Функция добавляет в БД победителя лота
 * @param mysqli $connect данные о подключении к базе данных
 * @param int $user ID пользователя, выигравшего лот
 * @param int $item ID лота, который выиграл пользователь
 * @return mysqli_result Результат запроса в БД
 */
function identify_winner_lot(mysqli $connect, int $user, int $item): ?mysqli_result
{
    $sql_winner = "UPDATE item SET winner_id = $user WHERE id = $item";

    $result_winner = mysqli_query($connect, $sql_winner);

    if (!$result_winner) {
        exit(error_output(500));
    }
    return $result_winner;
}

/**
 * Функция запроса на поиск в БД записи в таблице пользователей по введенному в форме email
 * @param mysqli $connect Данные о подключении к БД
 * @param string $check_email Введенный в форму e-mail пользователя
 * @return mysqli_result|false Переданный запрос в БД
 */
function verify_existence_email_db(mysqli $connect, string $check_email): ?mysqli_result
{
    $check_sql = "SELECT * FROM users WHERE email = ? LIMIT 1"; // запрос на поиск записи в таблице пользователей по переданному email
    return get_stmt_result($connect, $check_sql, [$check_email]);
}
