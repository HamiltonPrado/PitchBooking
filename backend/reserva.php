<?php
session_start();
if (!isset($_SESSION['atleta_id'])) {
    header('Location: login.html');
    exit;
}
require 'db.php';

$atleta_id = $_SESSION['atleta_id'];
$campo_id = $_POST['campo_id'];
$data_jogo = $_POST['data_jogo'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fim = $_POST['hora_fim'];
$usa_luz = isset($_POST['usa_luz']) ? 1 : 0;
$qtd_material = $_POST['qtd_material'];

// Verifica se o campo ja esta reservado nesse horario (anti-overbooking)
$stmt = mysqli_prepare($ligacao,
    "SELECT id FROM reserva WHERE campo_id = ? AND data_jogo = ? AND hora_inicio = ? AND estado = 'ativa'");
mysqli_stmt_bind_param($stmt, "iss", $campo_id, $data_jogo, $hora_inicio);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_fetch_assoc($resultado)) {
    die('Este campo já está reservado para essa data e hora.');
}

