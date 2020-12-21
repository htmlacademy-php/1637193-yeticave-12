<?php
require_once './functions/bootstrap.php'; //подключает все пользовательские функции и константы
require_once 'vendor/autoload.php'; //подключает файл автозагрузки composer

$connect = db_connection();

$user_name = '';
$item_title = '';
$item_id = '';

// указываем данные для доступа к SMTP-серверу
$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

//Создадим главный объект библиотеки SwiftMailer, ответственный за отправку сообщений.
// Передадим туда созданный объект с SMTP-сервером.
$mailer = new Swift_Mailer($transport);

//Сформируем запрос, который покажет список последних ставок по лотам без победителей,
// дата истечения которых меньше или равна текущей
$sql_bet_winner = 'SELECT bet.id AS bet_id, total, bet.user_id AS user_id, item_id, bet.created_at AS created_at, i.winner_id AS winner_id,
               i.title AS item_title, i.completed_at AS completed_at, u.name AS user_name, u.email AS user_email
        FROM bet
        INNER JOIN item i on bet.item_id = i.id
        INNER JOIN users u on bet.user_id = u.id
        INNER JOIN (
                SELECT item_id AS item_id_bet, MAX(total) AS max_bet
                FROM bet
                GROUP BY item_id
        ) bet_new ON bet.item_id = bet_new.item_id_bet AND bet.total = bet_new.max_bet
        WHERE i.winner_id IS NULL AND i.completed_at <= CURDATE()
        ORDER BY bet_id';


$result_bet_winner = mysqli_query($connect, $sql_bet_winner);

if (!$result_bet_winner) {
    exit('Ошибка запроса: &#129298; ' . mysqli_error($connect));
}

//Получим список победитилей в виде массива
if ($result_bet_winner && mysqli_num_rows($result_bet_winner)) {
    $winners = mysqli_fetch_all($result_bet_winner, MYSQLI_ASSOC);

    if (!empty($winners)) {
        foreach ($winners as $winner) {
            //Установим параметры сообщения: тема, отправитель и список его получателей
            $message = new Swift_Message();
            $message->setSubject("Ваша ставка победила");
            $message->setFrom(['keks@phpdemo.ru' => 'keks@phpdemo.ru']);
            $message->setTo([$winner['user_email'] => $winner['user_name']]);

            //данные для вставки в шаблон:
            $user_name = $winner['user_name'];
            $item_title = $winner['item_title'];
            $item_id = (int)$winner['item_id'];
            $host_url = $_SERVER['SERVER_NAME'];

            //Передадим список победителей в шаблон, используемый для отправки email
            $message_content = include_template(
                '/email.php',
                [
                    'user_name' => $user_name,
                    'item_title' => $item_title,
                    'item_id' => $item_id,
                    'host_url' => $host_url
                ]
            );
            $message->setBody($message_content, 'text/html');

            //Передадим в объект библиотеки SwiftMailer, ответственный за отправку сообщений, созданный объект с SMTP-сервером
            $mailer = new Swift_Mailer($transport);

            if ($winner['winner_id'] === null) {
                //Отправляем подготовленное сообщение и получаем результат
                $result = $mailer->send($message);

                $user = (int)$winner['user_id'];
                $item = (int)$winner['item_id'];

                $sql_winner = "UPDATE item SET winner_id = $user WHERE id = $item";

                $result_winner = mysqli_query($connect, $sql_winner);

                if (!$result_winner) {
                    exit('Ошибка запроса: &#129298; ' . mysqli_error($connect));
                }
            }
        }
    }
}
