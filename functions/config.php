<?php
$is_auth = rand(0, 1);

$user_name = 'Александр'; // указал здесь имя

//константы для работы с загрузкой файлов
define('NAME_FOLDER_UPLOADS_FILE', 'uploads');  //Папка для загрузки пользовательских изображений
define('_DS', DIRECTORY_SEPARATOR); //универсальный разделитель каталогов
define('UPLOAD_MAX_SIZE', 2097152); // максимальный размер загружаемый файлов 2Мб

//константы для обработки данных с датой и временем
define('CORRECT_DATE_TIME_FORMAT', 'Y-m-d H:i:s'); //формат времени ГГГГ-ММ-ДД ЧЧ:ММ:СС
define('CORRECT_DATE_TIME', 'Y-m-d'); //формат времени ГГГГ-ММ-ДД

//данные к подключению БД
const DB_CONNECTION_DATA = [
    'host' => 'localhost',
    'database' => 'yeticave',
    'user' => 'root',
    'password' => 'root'
];

define('TITLE_MAIN_PAGE', 'Yeticave - Главная страница by Alexander Galkin'); //Заголовок главной страницы
