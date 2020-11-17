<?php
/**
 * Возвращает оставшееся до переданной в функцию даты время в виде массива [ЧЧ, ММ].
 * @param string $get_end_date Конечная дата в формате 'ГГГГ-ММ-ДД'.
 * @return array Возвращает массив строк в формате [ЧЧ, ММ]. В случае некорректного формата введенной даты или истекшей на данный момент даты возвращает [0, 0].
 */
//функция для подсчёта оставшегося времени действия лота в аукционе
function get_date_range($get_end_date)
{
    //проверка корректности ввода даты на соответствие формату 'ГГГГ-ММ-ДД'
    if (!is_date_valid($get_end_date)) {
        return [0, 0];
    }
    date_default_timezone_set("Europe/Moscow");
    setlocale(LC_ALL, 'ru_RU');
    //сегодняшняя дата
    $current_date = time();
    //дата окончания аукциона
    $future_date = strtotime($get_end_date);

    // получает экземпляр временного промежутка на основе разницы между двумя датами
    $diff = $future_date - $current_date;

    //проверка даты на то, что она из прошлого времени
    if ($diff < 0) {
        return [0, 0];
    }
    // Приводит временной интервал к нужному формату
    $hours = floor($diff / 3600);
    $minutes = ceil(($diff % 3600) / 60);

    //дополняет строки, начиная слева, заданными символами до 2-х цифр
    $hours_format = str_pad($hours, 2, 0, STR_PAD_LEFT);
    $minutes_format = str_pad($minutes, 2, 0, STR_PAD_LEFT);

    return [$hours_format, $minutes_format];
}
