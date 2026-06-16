<?php
session_start();
if (!isset($_SESSION['atleta_id'])) {
    header('Location: login.html');
    exit;
}
require 'db.php';

$atleta_id = $_SESSION['atleta_id'];

$stmt = mysqli_prepare($ligacao,
    "SELECT nome, email, docs_verificados FROM atleta WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $atleta_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$atleta = mysqli_fetch_assoc($resultado);