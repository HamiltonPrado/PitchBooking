<?php
session_start();
require 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = mysqli_prepare($ligacao, "SELECT id, nome, password, role FROM atleta WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$atleta = mysqli_fetch_assoc($resultado);

//verificar a password e criar a sessão//
if ($atleta && password_verify($password, $atleta['password'])) {
    $_SESSION['atleta_id'] = $atleta['id'];
    $_SESSION['nome'] = $atleta['nome'];
    $_SESSION['role'] = $atleta['role'];
    echo 'Login com sucesso!';
} else {
    echo 'Email ou password incorretos.';
}