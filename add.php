<?php
/**
 * @var string $user_name
 * @var array $categories
 * @var boolean $is_auth
 */
require_once './helpers.php'; //дефолтные функции от создателей курса
require_once './functions/data.php'; //дефолтные переменные
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './functions/sql_connect.php'; //параметры подключения к базе данных
require_once './functions/check.php'; //функции, проверяющие введенные в форму данные на корректность

$connect = db_connection();
$categories = get_categories_from_db($connect);

if (!$connect) {
    $error = 'Ошибка подключения:' . mysqli_connect_error();
    $page_content = include_template('404_page.php', [
        'error' => $error,
        'categories' => $categories
    ]);
}

//первая версия кода добавления нового лота - раскомментируй для просмотра
////создание новой страницы для введенного из формы нового лота
//if ($_SERVER['REQUEST_METHOD'] == 'POST') {

// применение функций для проверки полей формы к каждому элементу формы внутри цикла
$required_fields = ['lot-name', 'category', 'message', 'lot-image', 'lot-price', 'lot-step', 'lot-date'];
$rules = [
    'lot-name' => function () {
        return validate_filled('lot-name');
    },
    'category' => function () {
        return validate_category('category');
    },
    'message' => function () {
        return validate_filled('message');
    },
    'lot-rate' => function () {
        return validate_number_value('lot-rate');
    },
    'lot-step' => function () {
        return validate_number_value('lot-step');
    },
    'lot-date' => function () {
        return validate_date_end('lot-date');
    }
];

//все еще первая версия кода добавления нового лота
//    foreach ($_POST as $key => $value) {
//        if (isset($rules[$key])) {
//            $rule = $rules[$key];
//            $errors[$key] = $rule();
//        }
//    }
//    $errors['lot-img'] = validate_image('lot-img', ['image/png', 'image/jpeg', 'image/jpg']);
//
//    $errors = array_filter($errors);
//
//    // подготовленное выражение для вставки в БД
//    $file_url = save_file('lot-img');
//    if (!count($errors) && $connect && isset($categories) && $file_url) {
//        $sql_add_lot = 'INSERT INTO item (completed_at, category_id, author_id, title, description, image_url, start_price, bet_step) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
//        $stmt = db_get_prepare_stmt($connect, $sql_add_lot, [$_POST['lot-date'], $_POST['category'], 1, $_POST['lot-name'], $_POST['message'], $file_url, $_POST['lot-rate'], $_POST['lot-step']]);
//
//        $result_stmt = mysqli_stmt_execute($stmt);
//        if (!$result_stmt) {
//            $page_content = include_template('404.php', ['error' => mysqli_error($connect)]);
//        }
//        $last_id = mysqli_insert_id($connect);
//        header('Location: lot.php?id=' . $last_id);
//        die();
//    }
//}
//
//    $page_content = include_template('add_lot.php', compact('categories', 'lot', 'errors'));
//
//    $layout_content = include_template('layout.php', [
//        'content' => $page_content,
//        'categories' => $categories,
//        'title' => 'Добавление нового лота',
//        'user_name' => $user_name,
//        'is_auth' => 1
//    ]);
//
//    print($layout_content);



//вторая версия кода добавления нового лота
$added_lot = [];
$errors = [];

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена
    $new_lot['author_id'] = 1;
    $new_lot['created_at'] = date('Y-m-d H:i:s');

    //Валидация файла
    $errors['lot-img'] = validate_file('lot-img', '/uploads/');
    if ($errors['lot-img'] === NULL) {
        if (move_uploaded_file($_FILES['lot-img']['tmp_name'], FILE_PATH . $_FILES['lot-img']['name'])) {
            $added_lot['lot-img'] = NAME_FOLDER_UPLOADS_FILE . $_FILES['lot-img']['name'];
        } else {
            $errors['lot-img'] = 'Ошибка при перемещении файла ';
        }
    }

    //Валидация соответствующих полей и сохранение ошибок (при наличии)
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }
    $errors = array_filter($errors);  //убираем пустые значения в массиве

    //Если были ошибки валидации - возвращаем на страницу создания нового лота с показом ошибок
    if ($errors) {
        $page_content = include_template('add_lot.php', ['categories' => $categories, 'errors' => $errors]);
        $layout_content = include_template('layout.php', ['categories' => $categories, 'content' => $page_content, 'name_page' => 'Добавление new лота', 'user_name' => $user_name, 'is_auth' => $is_auth]);
        print($layout_content);

    } else { //Если ошибок не было - добавляем новый лот в БД

        $sql_add_lot = 'INSERT INTO item (created_at, title, description, image_url, start_price, completed_at, bet_step, author_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        //Подготавливает SQL выражение к выполнению
        $stmt = mysqli_prepare($connect, $sql_add_lot);
        //Привязка переменных к параметрам подготавливаемого запроса
        mysqli_stmt_bind_param($stmt, 'sssssssii', $added_lot['created_at'], get_post_value('lot-name'), get_post_value('message'), $added_lot['lot-img'], get_post_value('lot-rate'), get_post_value('lot-date'), get_post_value('lot-step'), $added_lot['author'], get_post_value('category'));

        if (!mysqli_stmt_execute($stmt)) { //если подготовленный запрос не выполнился
            $error = mysqli_error($connect);
            exit("Ошибка MySQL: " . $error);
        }

        //Возвращает автоматически генерируемый ID, используя последний запрос
        $id_last_added_lot = mysqli_insert_id($connect);
        if ($id_last_added_lot === 0) {
            exit('Ошибка получения id последней добавленной записи');
        }

        //Получаем из БД данные по только что добавленному лоту
        $sql_read_lot = "SELECT item.created_at, item.title, item.description, item.image_url, item.start_price, item.completed_at, item.bet_step, category.title AS name_category
                         FROM item
                         JOIN category ON item.category_id = category.id
                         WHERE item.id ='" . $id_last_added_lot . "'";
        //Выполняет запрос к базе данных
        $result_lot = mysqli_query($connect, $sql_read_lot);
        //Выбирает одну строку из результирующего набора и помещает ее в ассоциативный массив
        $open_lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);

        if ($open_lot === NULL) {
            http_response_code(404);
            exit("Страница с id =" . $id_last_added_lot . " не найдена.");
        }

        //Перенаправляем на страницу с только что добавленным лотом
        header('Location: lot.php?id=' . $id_last_added_lot);
    }
} else {  //Если форма не отправлена, показываем страницу добавления лота
    $page_content = include_template('add_lot.php', ['categories' => $categories, 'errors' => $errors]);
    $layout_content = include_template('layout.php', ['categories' => $categories, 'content' => $page_content, 'name_page' => 'Добавление лота', 'user_name' => $user_name, 'is_auth' => $is_auth]);
    print($layout_content);
}
