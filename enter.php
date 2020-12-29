<?php
/**
 * @var string $user_name
 * @var string $is_auth
 */
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы

$connect = db_connection();
$categories = get_categories_from_db($connect);
$page_content = '';

if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
    //если форма не отправлена, то проверяем наличие сессии у пользователя:
    //сессии нет: показываем форму для захода на сайт
    $page_content = include_template('/enter_page.php', [
        'categories' => $categories
    ]);

    //сессия есть: пользователь уже залогинен и ему нужно показать главную
    if (isset($_SESSION['user'])) {
        error_output(403, 'enter');
    }
} else {//Проверяем, что форма была отправлена
    $form = $_POST;
    $errors = validate_if_filled_in(); //проверяем, заполнены ли поля в форме авторизации пользователя

    //Проверим, есть ли в БД пользователь с переданным в форме email.
    $check_email = strtolower($form['email']);
    $check_result_sql = verify_existence_email_db($connect, $check_email);

    // Если в результате проверки пользователь есть в результате запроса из БД,
    // тогда эти данные о нем получаем в виде нового ассоциативного массива,
    // иначе данных о пользователе в БД еще нет
    $user = $check_result_sql ? mysqli_fetch_array($check_result_sql, MYSQLI_ASSOC) : null;

    if (!count($errors) and $user) {
        //Проверяем, что сохраненный хеш пароля и введенный пароль из формы совпадают.
        if (password_verify($form['password'], $user['password'])) {
            //если совпадение есть, значит пользователь указал верный пароль.
            // Тогда мы можем открыть для него сессию и записать в неё все данные о пользователе
            $_SESSION['user'] = $user;
            redirect_to_main();
        } else { //иначе пароль неверный и мы добавляем сообщение об этом в список ошибок
            $errors['form'] = 'Вы ввели неверный email/пароль';
        }
    } elseif (empty($errors['email'])) { //Если пользователь не найден, то записываем это как ошибку валидации
        $errors['form'] = 'Вы ввели неверный email/пароль';
    }

    //Если были ошибки, значит мы снова должны показать форму входа, передав в шаблон список полученных ошибок
    if (count($errors)) {
        $page_content = include_template('/enter_page.php', ['form' => $form, 'errors' => $errors]);
    } else { //ошибок нет, значит аутентификация успешна: возвращаем пользователя на главную
        redirect_to_main();
    }
}

$layout_content = include_template('/layout.php', [
    'content' => $page_content,
    'categories' => $categories,
    'title' => 'Тут можно залогиниться',
    'user_name' => $user_name,
    'is_auth' => $is_auth
]);

print($layout_content);
