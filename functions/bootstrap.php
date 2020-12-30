<?php
require_once './functions/session.php'; //старт сессии для пользователя
require_once './functions/config.php'; //пользовательские константы и данные по подключению к БД и SMTP-серверу
require_once './functions/sql_connect.php'; //функции, работающие с подключением к базе данных
require_once './functions/check.php'; //функции, проверяющие введенные в форму данные на корректность
require_once './functions/numbers.php'; //числовые функции
require_once './functions/time.php'; //функции, влияющие на обработку времени
require_once './helpers.php'; //дефолтные функции от создателей курса
