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
        'expiration_date' => '2020-10-26'
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

