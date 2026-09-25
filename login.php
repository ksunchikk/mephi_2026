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

$query = "SELECT name, pwd_hash FROM user WHERE login = '$login'";

$result = $mysqli->prepare($query);
$result->execute();

$data = $result->get_result();

$user_data = $data->fetch_assoc();


// Хэширование пароля
// $pwd_hash = password_hash('12345', PASSWORD_DEFAULT);
// echo $pwd_hash;


// echo "Логин: $login, пароль: $pwd";

// $pwd === $user_data['pwd_hash']

if(password_verify($pwd, $user_data['pwd_hash'])) {
    $_SESSION['name'] = $user_data['name'];
    echo "<h1>Добро пожаловать, " . $_SESSION['name'] . "!</h1>";
}
else {
    echo "<h1>Неправильный логин или пароль!</h1>";
}