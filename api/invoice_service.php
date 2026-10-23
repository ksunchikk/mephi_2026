<?php
    session_start();
    if(!isset($_SESSION['name'])) {
        http_response_code(401);
        echo('Вы не вошли в аккаунт!');
        exit;
    }

    $allowed = ['operation_date', 'operation', 'x', 'y', 'z'];

    $date_from = '2000-01-01'; // очень давно
    $date_to = '2100-01-01'; // очень нескоро

    if(!empty($_GET['date_from'])) {
        $date_from = $_GET['date_from'];
    }

    if(!empty($_GET['date_to'])) {
        $date_to = $_GET['date_to'];
    }

    $sort = 'operation_date';
    $dir = 'DESC';

    if(isset($_GET['sort']) && in_array($_GET['sort'], $allowed)) {
        $sort = $_GET['sort'];
    }

    if(isset($_GET['dir']) && $_GET['dir'] == 'ASC') {
        $dir = $_GET['dir'];
    }

    $host = 'localhost';
    $username = 'root';
    $password_db = '';
    $db = 'calc';

    $limit = 5;

    $page = 1;

    if(!empty($_GET['page'])) {
        $page = (int) $_GET['page'];
    }

    if ($page < 1) {
        $page = 1;
    }

    $offset = ($page - 1) * $limit;

    $mysqli = new mysqli($host, $username, $password_db, $db);

    // Обработка ошибки подключения к БД
    if($mysqli->connect_error) {
        die('Ошибка подключения к БД');
    }

    $login = $_SESSION['login'];

    $query = "SELECT operation_date, x, y, z, operation FROM operation WHERE login = ? AND DATE(operation_date) BETWEEN ? AND ? ORDER BY $sort $dir LIMIT ? OFFSET ?";

    $result = $mysqli->prepare($query);
    $result->bind_param('sssdd', $login, $date_from, $date_to, $limit, $offset);

    $result->execute();

    $calcs_db = $result->get_result();

    $calcs = array();

    while($row = $calcs_db->fetch_assoc()) {
        $calcs[] = $row;
    }

    $result->close();
    $mysqli->close();

    echo json_encode($calcs);