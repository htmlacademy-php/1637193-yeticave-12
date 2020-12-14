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

/**
 * Фукнция подготавливает вывод сложной пагинации.
 * Итоговый вариант выглядит следующим образом (нынешняя страница на примере под №11): " 1 2 3 ... 8 9 10 11 12 13 14 ... 37 38 39 "
 * @param $pages_array array Массив, состоящий из номеров страниц для пагинации.
 * @param $current_page int Номер текущей страницы.
 */
function get_pagination($pages_array, $current_page) {
    if (count($pages_array) > PAGE_LIMIT_SIDE_PAGINATION) { //если число страниц с результатами поиска больше 7, тогда нужен сложный вывод пагинации:
        //копируем в отдельные массивы значения от края до номера текущей страницы
        $pages_left_side = array_slice($pages_array, 0, $current_page - 1);
        $pages_right_side = array_slice($pages_array, $current_page);

        //копируем в отдельные массивы по 3 крайних (min и max) значения
        $pages_left_end = array_splice($pages_left_side, 0, PAGE_LIMIT_PAGINATION);
        $pages_right_end = array_splice($pages_right_side, -3, PAGE_LIMIT_PAGINATION);

        //вырезаем в отдельные массивы значения без 3х крайних элементов до номера текущей страницы
        $pages_left_center = array_splice($pages_left_side, -3, PAGE_LIMIT_PAGINATION);
        $pages_right_center = array_splice($pages_right_side, 0, PAGE_LIMIT_PAGINATION);

        $separator = ['...']; //разделитель
        $current_page_elem = [$current_page]; //создаем новый массив со значением текущего номера страницы

        //вывод пагинации для левых 7-ми страниц:
        if (($current_page <= (PAGE_LIMIT_SIDE_PAGINATION))) {
            $new_pages_array = array_merge($pages_left_end, $pages_left_center, $current_page_elem, $pages_right_center, $separator, $pages_right_end);
        } //вывод пагинации для страниц, расположенных через 7 от начала и за 7 до конца пагинации
        elseif (($current_page > PAGE_LIMIT_SIDE_PAGINATION) && ($current_page <= (count($pages_array) - PAGE_LIMIT_SIDE_PAGINATION))) {
            $new_pages_array = array_merge($pages_left_end, $separator, $pages_left_center, $current_page_elem, $pages_right_center, $separator, $pages_right_end);
        } //вывод пагинации для правых 7-ми страниц:
        elseif (($current_page > PAGE_LIMIT_SIDE_PAGINATION) && ($current_page > count($pages_array) - PAGE_LIMIT_SIDE_PAGINATION)) {
            $new_pages_array = array_merge($pages_left_end, $separator, $pages_left_center, $current_page_elem, $pages_right_center, $pages_right_end);
        }
        return $new_pages_array;
    }
    return $pages_array;
}
