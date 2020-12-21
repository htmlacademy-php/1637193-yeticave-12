<?php
/**
 * Функция для подсчёта оставшегося времени действия лота в аукционе
 * Возвращает оставшееся до переданной в функцию даты время в виде массива [ЧЧ, ММ].
 * @param string $get_end_date Конечная дата в формате 'ГГГГ-ММ-ДД'.
 * @return array Возвращает массив строк в формате [ЧЧ, ММ]. В случае некорректного формата введенной даты или истекшей на данный момент даты возвращает [0, 0].
 */
function get_date_range($get_end_date)
{
    //проверка корректности ввода даты на соответствие формату 'ГГГГ-ММ-ДД'
    if (!is_date_valid($get_end_date, CORRECT_DATE_TIME_FORMAT)) {
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

/**
 * Функция объединяет в строке число значение прошедшего времени с с момента последнего добавления ставки и текстового описания единицы времени
 * @param $bet_time дата и время добавления ставки
 * @return string возвращает описание времени, прошедшего с момента последнего добавления ставки, в удобном для чтения формате
 */
function get_correct_bet_time($bet_time)
{
    $bet_time_strtotime = strtotime('now') - strtotime($bet_time);
    $bet_time_minutes = ceil($bet_time_strtotime / 60);
    $bet_time_hours = floor($bet_time_strtotime / 3600);
    $ago = ' назад';
    if ((int)$bet_time_hours === 0 && (int)$bet_time_minutes === 0 && (int)$bet_time_strtotime < 60) {
        return 'Только что';
    } elseif ((int)$bet_time_hours === 0 && ((int)$bet_time_minutes >= 1 || ((int)$bet_time_minutes < 60))) {
        return $bet_time_minutes . ' ' . get_noun_plural_form($bet_time_minutes, 'минута', 'минуты', 'минут') . $ago;
    } else if ((int)$bet_time_hours === 1) {
        return 'час' . $ago;
    } else if ((int)$bet_time_hours > 1 && (int)$bet_time_hours < 12) {
        return $bet_time_hours . ' ' . get_noun_plural_form($bet_time_hours, 'час', 'часа', 'часов') . $ago;
    } else {
        return date_format(date_create($bet_time), 'd.m.y в H:i');
    }
}
