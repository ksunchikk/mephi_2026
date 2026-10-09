<?php
    session_start();
    // Проверка может ли пользователь выполнить действие
    if(!isset($_SESSION['name'])) {
        // echo("<h2>Вы не вошли в аккаунт! <br /> Через 3 секунды вы будете перенаправлены на страницу логина</h2> <br />");
        // echo("<a href='login.html'>Войти в аккаунт</a>");
        // echo("<meta http-equiv='refresh' content='3; url=login.html'></meta>");
        // die();
        http_response_code(401);
        echo('Вы не вошли в аккаунт!');
        exit;
    }
    $login = $_SESSION['login'];
    $operation = $_REQUEST['operation'];
    $x = $_REQUEST['x'];
    $y = $_REQUEST['y'];

    $allowed = ['plus', 'minus', 'mult'];

    if(!in_array($operation, $allowed, true)) {
        http_response_code(400);
        echo('Операция не содержится в списке допустмых!');
        exit;
    }

    if(!is_numeric($x) || !is_numeric($y)) {
        http_response_code(400);
        echo('Операнды x и y должны быть числами!');
        exit;
    }

    // Подключиться к БД
    $host = 'localhost';
    $username = 'root';
    $password_db = '';
    $db = 'calc';

    $mysqli = new mysqli($host, $username, $password_db, $db);

    // Обработка ошибки подключения к БД
    if($mysqli->connect_error) {
        die('Ошибка подключения к БД');
    }
    // Подготовить запрос

    $query = "INSERT INTO operation (login, operation, x, y, z) VALUES(?, ?, ?, ?, ?)";

    $z = 0;
    // Провести вычисления 

    if($operation === 'plus'){
        $z = $x + $y;
    }
    else if($operation === 'minus'){
        $z = $x - $y;
    }
    else {
        $z = $x * $y;
    }

    // Выполнение запроса
    // Работа по отправке запроса
    // Сообщаем mysqli какой запрос выполнить 
    $result = $mysqli->prepare($query);

    // Прокидыаю внутрь запроса параметры вместо ?

    $result->bind_param('ssddd', $login, $operation, $x, $y, $z);
    $result->execute();

    $result->close();
    $mysqli->close();

    // Направить результат на клиент для вывода на интерфейсе

    sleep(3);

    echo($z);