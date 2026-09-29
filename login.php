<?php
session_start();
// ОЧЕНЬ ПЛОХО!!!
// $valid_login = 'admin';
// $valid_pwd = '12345';

// ОЧЕНЬ ПЛОХО!!!
$host = 'localhost';
$username = 'root';
$password_db = '';
$db = 'calc';

$mysqli = new mysqli($host, $username, $password_db, $db);

// $_REQUEST состоть из 3х частей: POST, GET и Cookie
$login = $_POST['login'];
$pwd = $_POST['pwd'];
// Плохая практика
// $query = "SELECT name, pwd_hash FROM user WHERE login = '$login'";
$query = "SELECT name, pwd_hash, login FROM user WHERE login = ?";

$result = $mysqli->prepare($query);

// Передаем 2 и более аргументов, 1 - обязательный (это типы данных)
// Нельзя передавать значения на прямую, передаем только в виле ссылок и переменных
$result->bind_param("s", $login);
$result->execute();

$data = $result->get_result();

$user_data = $data->fetch_assoc();


// Хэширование пароля
// $pwd_hash = password_hash('12345', PASSWORD_DEFAULT);
// echo $pwd_hash;


// echo "Логин: $login, пароль: $pwd";

// $pwd === $user_data['pwd_hash']

$password = isset($user_data['pwd_hash']) ? $user_data['pwd_hash'] : '';

if(password_verify($pwd, $password)) {
    $_SESSION['name'] = $user_data['name'];
    $_SESSION['login'] = $user_data['login'];
    echo "<h1>Добро пожаловать, " . $_SESSION['name'] . "!</h1>";
    echo("<meta http-equiv='refresh' content='3; url=calc.php' />");
}
else {
    echo "<h1>Неправильный логин или пароль!</h1>";
    echo("<meta http-equiv='refresh' content='3; url=login.html'></meta>");
}

$result->close();
    $mysqli->close();