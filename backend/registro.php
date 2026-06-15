<?php
require 'db.php';
$nome = $_POST['nome'];
$email = $_POST['email'];
$password = $_POST['password'];
$tipo_doc = $_POST['tipo_doc'];
$num_doc = $_POST['num_doc'];
$nif = $_POST['nif'];

$stmt = mysqli_prepare($ligacao, "SELECT id FROM atleta WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (mysqli_fetch_assoc($resultado)) {
    die('Este email já está registado.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);