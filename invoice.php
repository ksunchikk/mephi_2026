<?php
  session_start();
  if(!isset($_SESSION['name'])) {
    echo("<h2>Вы не вошли в аккаунт! <br /> Через 3 секунды вы будете перенаправлены на страницу логина</h2> <br />");
    echo("<a href='login.html'>Войти в аккаунт</a>");
    echo("<meta http-equiv='refresh' content='3; url=login.html'></meta>");
    die();
  }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Счет за услуги</title>
    <style>
        td, th {
            border: 1px solid white;
            text-align: center;
            padding: 5px;
            background-color: #ebafd7;
        }
        td {
            background-color: #dadada;
        }

        .filters {
            margin-block: 10px;
        }
    </style>
    <script>
        function loadData() {
            // Ожидание ответа
            document.getElementById("calcs").innerHTML = '<tr><td colspan="5">Загрузка...</td></tr>';

            const date_from = document.getElementById('date_from').value;
            const date_to = document.getElementById('date_to').value;

            const params = new URLSearchParams({
                date_from: date_from,
                date_to: date_to
            })

            // Запрашиваем данные

            const xhr = new XMLHttpRequest();

            xhr.open('GET', 'api/invoice_service.php?' + params.toString(), true);
            xhr.onload = function () {
                if (xhr.status === 401) {
                    window.location.href = "login.html";
                }
                
                else if (xhr.status === 200) {
                    const calcs = JSON.parse(xhr.responseText);
                    showCalcs(calcs);
                }
                else {
                    alert(`Ошибка ${xhr.status}: ${xhr.responseText}`)
                }
            }

            xhr.send();
        }

        function showCalcs(calcs){
            if(calcs.length === 0) {
                document.getElementById("calcs").innerHTML = '<tr><td colspan="5">Ничего не найдено</td></tr>';
            }
            else {
                const html = calcs.map((row) => `<tr>
                <td>${row.operation_date}</td>
                <td>${row.x}</td>
                <td>${row.y}</td>
                <td>${row.operation}</td>
                <td>${row.z}</td>
                </tr>`).join('');
                document.getElementById("calcs").innerHTML = html;
            }
        }

        function resetFilter() {
            document.getElementById('date_from').value = '';
            document.getElementById('date_to').value = '';

            loadData();
        }
        window.onload = loadData;
    </script>
</head>
<body>
    <h1>За прошедший период вы совершили следующие операции</h1>

    <div class="filters">
        <h3>Фильтры</h3>
        <label>C: </label>
        <input type="date" id="date_from"/>
        <label>По: </label>
        <input type="date" id="date_to" />

        <button onclick="loadData()">Применить</button>
        <button onclick="resetFilter()">Сбросить</button>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Время</th>
                <th>x</th>
                <th>y</th>
                <th>Операция</th>
                <th>Результат</th>
            </tr>
        </thead>
        <tbody id="calcs">
            <tr>
                <td colspan="5">Загрузка...</td>
            </tr>
        </tbody>
    </table>
</body>
</html>