<?php
/**
 * Функция по выводу форматированной суммы товара
 * @param integer $lot_price Вводится число от суммы товара
 * @return string Возвращается строка в виде отформатированного числа, с пробелами каждые 3 порядка и знаком ₽ в конце
 */
function formatted_sum($lot_price)
{
    $round_number = ceil($lot_price);
    if ($round_number < 1000) {
        $round_number .= ' ' . '₽';
        return $round_number;
    }
    //number_format — Форматирует число с разделением групп
    $round_number = number_format($round_number, 0, ',', ' ') . ' ' . '₽';

    return $round_number;
}

/**
 * Функция заполняет данными шаблон пагинации
 * @param int $pages_count количество страниц, которые нужны для вывода результата
 * @param int $current_page Номер текущей страницы
 * @param string $search_page Адрес страницы, с которой выполняется поиск
 * @param string $search Текстовое содержимое поискового запроса [необязательный параметр]
 * @param string $category_id ID категории лотов [необязательный параметр]
 * @return html Заполненный HTML-шаблон пагинации
 */
function get_pagination(int $pages_count, int $current_page, string $search_page, string $search = '', string $category_id = ''): ?string
{
    $fill_pages = range(1, $pages_count); //Заполняем массив номерами всех страниц

    $pages = get_difficult_pagination($fill_pages, $current_page); //проверяем, нужна ли сложная пагинация

// выводим на отдельный шаблон пагинации, который подключен к странице поиска.
    return include_template('/pagination.php', [
        'pages_count' => $pages_count,
        'pages' => $pages,
        'search' => $search,
        'current_page' => $current_page,
        'search_page' => $search_page,
        'category_id' => $category_id
    ]);
}

/**
 * Фукнция подготавливает вывод сложной пагинации.
 * Итоговый вариант выглядит следующим образом (нынешняя страница на примере под №11): " 1 2 3 ... 8 9 10 11 12 13 14 ... 37 38 39 "
 * @param array $pages_array Массив, состоящий из номеров страниц для пагинации.
 * @param int $current_page Номер текущей страницы.
 * @return array Массив, состоящий из элементов сложной пагинации.
 */
function get_difficult_pagination(array $pages_array, int $current_page): array
{
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
