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

// Vai buscar os precos do campo escolhido
$stmt = mysqli_prepare($ligacao,
    "SELECT preco_base, custo_luz, custo_material FROM campo WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $campo_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$campo = mysqli_fetch_assoc($resultado);

// Calcula o total: base + luz (se ativada) + material (por unidade)
$valor_total = $campo['preco_base'];
if ($usa_luz) {
    $valor_total = $valor_total + $campo['custo_luz'];
}
$valor_total = $valor_total + ($campo['custo_material'] * $qtd_material);