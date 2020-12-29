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

//константы для работы с поиском на сайте и пагинацией
/**
 * Число лотов, которые выходят на одной странице поиска после запроса пользователя
 */
define('LIMIT_OF_SEARCH_RESULT', 9);

/**
 * Число страниц, которое отображается в пагинации вокруг текущей страницы и по краям, при многостраничном выводе на странице поиска
 */
define('PAGE_LIMIT_PAGINATION', 3);

/**
 * Число страниц, которое выводится по краям без разделителя, в пагинации на странице поиска
 */
define('PAGE_LIMIT_SIDE_PAGINATION', 7);

/**
 * Данные для подключения к БД
 */
const DB_CONNECTION_DATA = [
    'host' => 'localhost',
    'database' => 'yeticave',
    'user' => 'root',
    'password' => 'root'
];

/**
 * Данные для доступа к SMTP-серверу
 */
const SMTP_CONFIG = [
    'host' => 'smtp.mailtrap.io',
    'username' => 'bd2f8a93ea7c17',
    'password' => '9882134b53e1fc',
    'port' => '2525',
    'encryption' => 'tls'
];
