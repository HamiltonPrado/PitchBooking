<?php
session_start();
if (!isset($_SESSION['atleta_id'])) {
    header('Location: ../frontend/login.html');;
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

// Verifica se os documentos foram validados pelo backoffice
if ($atleta['docs_verificados'] == 0) {
    echo 'Os seus documentos ainda não foram verificados.';
} else {
    echo 'Documentos verificados.';
}