<?php
$is_auth = rand(0, 1);

$user_name = 'Александр'; // указал здесь имя

//массив с названиями категорий
$categories = [
    "Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"
];

//массив с информацией о 6 товарах в рекламных объявлениях
$ad_information = [
    [
        'title' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 10999,
        'url_image' => 'img/lot-1.jpg',
        'expiration_date' => '2020-10-28'
    ],
    [
        'title' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 159999,
        'url_image' => 'img/lot-2.jpg',
        'expiration_date' => '2020-10-29'
    ],
    [
        'title' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => 8000,
        'url_image' => 'img/lot-3.jpg',
        'expiration_date' => '2020-10-31'
    ],
    [
        'title' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => 10999,
        'url_image' => 'img/lot-4.jpg',
        'expiration_date' => '2020-11-15'
    ],
    [
        'title' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => 7500,
        'url_image' => 'img/lot-5.jpg',
        'expiration_date' => '2020-11-28'
    ],
    [
        'title' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => 5400,
        'url_image' => 'img/lot-6.jpg',
        'expiration_date' => '2021-06-28'
    ]
];


/**
 * Возвращает оставшееся до переданной в функцию даты время в виде массива [ЧЧ, ММ]
 * @param string $get_end_date Конечная дата в формате 'ГГГГ-ММ-ДД'
 * @return array Возвращает массив строк в формате [ЧЧ, ММ].
 */
//функция для подсчёта оставшегося времени действия лота в аукционе
function get_date_range($get_end_date)
{
        date_default_timezone_set("Europe/Moscow");
        setlocale(LC_ALL, 'ru_RU');
        //сегодняшняя дата
        $current_date = time();
        //дата окончания аукциона
		$future_date = strtotime($get_end_date);

		// получает экземпляр временного промежутка на основе разницы между двумя датами
		$diff = $future_date - $current_date;

		// Приводит временной интервал к нужному формату
        $hours = floor($diff / 3600);
        $minutes = ceil(($diff % 3600) / 60);

        //дополняет строки, начиная слева, заданными символами до 2-х цифр
        $hours_format = str_pad($hours, 2, 0, STR_PAD_LEFT);
        $minutes_format = str_pad($minutes, 2, 0, STR_PAD_LEFT);

    return [$hours_format, $minutes_format];
}
//функция по выводу форматированной суммы товара
function formatted_sum ($lot_price)
{
    $round_number = ceil($lot_price);
    if ($round_number < 1000) {
        $round_number .= ' ' . '₽';
        return $round_number;
    }
    //number_format — Форматирует число с разделением групп
     $round_number = number_format($round_number, 0, ',', ' ')  . ' ' . '₽';

    return $round_number;
}

/**
 * Функция подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

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
