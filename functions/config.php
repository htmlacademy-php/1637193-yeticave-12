<?php
/**
 * Статус авторизации пользователя
 */
$is_auth = isset($_SESSION['user']['name']) ?? false;

/**
 * Имя авторизованного пользователя
 */
$user_name = $_SESSION['user']['name'] ?? "";


//константы для работы с загрузкой файлов:
/**
 * Папка для загрузки пользовательских изображений
 */
 define('NAME_FOLDER_UPLOADS_FILE', 'uploads');

/**
 * Универсальный разделитель каталогов
 */
define('_DS', DIRECTORY_SEPARATOR);

/**
 * Максимальный размер загружаемый файлов 2Мб
 */
define('UPLOAD_MAX_SIZE', 2097152);


// константы для обработки данных с датой и временем:
/**
 * Формат времени ГГГГ-ММ-ДД ЧЧ:ММ:СС
 */
define('CORRECT_DATE_TIME_FORMAT', 'Y-m-d H:i:s');

/**
 * Формат времени ГГГГ-ММ-ДД
 */
define('CORRECT_DATE_TIME', 'Y-m-d');


/**
 * Число лотов, которые выходят на одной странице поиска после запроса пользователя
 */
define('LIMIT_OF_SEARCH_RESULT', 9);

/**
 * Данные по подключению к БД
 */
const DB_CONNECTION_DATA = [
    'host' => 'localhost',
    'database' => 'yeticave',
    'user' => 'root',
    'password' => 'root'
];
