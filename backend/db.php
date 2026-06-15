<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'PitchBooking';
$port = 3306;

$ligacao = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$ligacao) {
    die('Erro de ligação a base de dados: ' . mysqli_connect_error());
}
mysqli_set_charset($ligacao, 'utf8mb4');