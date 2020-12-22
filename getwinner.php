<?php
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы
require_once 'vendor/autoload.php'; //подключает файл автозагрузки composer

$connect = db_connection();

// список последних ставок по лотам без победителей, дата истечения которых меньше или равна текущей
$result_finished_lots = get_finished_lots($connect);

//Получим список победитилей в виде массива
if ($result_finished_lots && mysqli_num_rows($result_finished_lots)) {
    $winners = mysqli_fetch_all($result_finished_lots, MYSQLI_ASSOC);

    //определяем победителей конкретных лотов
    foreach ($winners as $winner) {
        if ($winner['winner_id'] === null) {

            $user = (int)$winner['user_id'];
            $item = (int)$winner['item_id'];

            identify_winner_lot($user, $item);
        }
    }
    if (!empty($winners)) {
        //отправка письма победителю лота
        array_map("send_email_to_winner", $winners);
    }
}
