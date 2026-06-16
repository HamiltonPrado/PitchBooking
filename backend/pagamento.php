<?php
session_start();
if (!isset($_SESSION['atleta_id'])) {
   header('Location: ../frontend/login.html');
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

// Regista o pagamento na base de dados
$stmt = mysqli_prepare($ligacao,
    "INSERT INTO pagamento (reserva_id, montante, tipo, operador_id) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "idsi", $reserva_id, $montante, $tipo, $operador_id);
mysqli_stmt_execute($stmt);

echo 'Pagamento registado com sucesso!';