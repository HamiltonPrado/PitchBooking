<?php
session_start();
if (!isset($_SESSION['atleta_id'])) {
    header('Location: login.html');
    exit;
}
require 'db.php';

// Só rececionista ou gestor podem registar pagamentos
if ($_SESSION['role'] != 'rececionista' && $_SESSION['role'] != 'gestor') {
    die('Não tem permissão para registar pagamentos.');
}

$reserva_id = $_POST['reserva_id'];
$montante = $_POST['montante'];
$tipo = $_POST['tipo'];
$operador_id = $_SESSION['atleta_id'];   