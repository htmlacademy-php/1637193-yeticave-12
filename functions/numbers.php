<?php
/**
 * Функция по выводу форматированной суммы товара
 * @param integer $lot_price Вводится число от суммы товара
 * @return string Возвращается строка в виде отформатированного числа, с пробелами каждые 3 порядка и знаком ₽ в конце
 */
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

