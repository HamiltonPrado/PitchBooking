<?php
session_start();
if (!isset($_SESSION['atleta_id'])) {
    header('Location: ../frontend/login.html');;
    exit;
}
require 'db.php';
header('Content-Type: application/json');

$atleta_id = $_SESSION['atleta_id'];

// Vai buscar os dados do atleta e o estado dos documentos
$stmt = mysqli_prepare($ligacao,
    "SELECT nome, email, tipo_doc, num_doc, nif, docs_verificados FROM atleta WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $atleta_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($resultado);