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
 * Функция выполняет выражение на основе подготовленного SQL-запроса и возвращает его результат
 * @param mysqli $connect Данные о подключении к БД
 * @param string $sql_result_count SQL-запрос в БД
 * @param array $array_stmt Данные для вставки на место плейсхолдеров
 * @return mysqli_result Результат подготовленного выражения
 */
function get_stmt_result(mysqli $connect, string $sql_result_count, $array_stmt = []): mysqli_result
{
    $stmt = db_get_prepare_stmt($connect, $sql_result_count, $array_stmt); //Подготовка SQL запроса к выполнению
    mysqli_stmt_execute($stmt); //Выполним подготовленное выражение
    $result_stmt = mysqli_stmt_get_result($stmt); //получим его результат

    if (!$result_stmt) {
        http_response_code(500);
        $error = 'Произошла ошибка: 500 &#129298; ';
        $error_description = 'Не удалось связать подготовленное выражение. &#128532; ';
        $error_link = '/index.php';
        $error_link_description = 'Предлагаем вернуться на главную.';
        $page_content = include_template(
            '/error_page.php',
            [
                'error' => $error,
                'error_description' => $error_description,
                'error_link' => $error_link,
                'error_link_description' => $error_link_description
            ]
        );
        $layout_content = include_template('/layout.php', [
            'content' => $page_content,
            'categories' => [],
            'title' => 'Ошибка 500',
            'user_name' => $user_name,
            'is_auth' => $is_auth
        ]);

        exit($layout_content);
    }
    return $result_stmt;
}

/**
 * Функция заполняет данными шаблон пагинации
 * @param string $search Текстовое содержимое поискового запроса
 * @param int $pages_count количество страниц, которые нужны для вывода результата
 * @param int $current_page Номер текущей страницы
 * @param string $search_page Адрес страницы, с которой выполняется поиск
 * @return html Заполненный HTML-шаблон пагинации
 */
function get_pagination(string $search, int $pages_count, int $current_page, string $search_page): ?string
{
    $fill_pages = range(1, $pages_count); //Заполняем массив номерами всех страниц

    $pages = get_difficult_pagination($fill_pages, $current_page); //проверяем, нужна ли сложная пагинация

// выводим на отдельный шаблон пагинации, который подключен к странице поиска.
    return include_template('/pagination.php', [
        'pages_count' => $pages_count,
        'pages' => $pages,
        'search' => $search,
        'current_page' => $current_page,
        'search_page' => $search_page
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
