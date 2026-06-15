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