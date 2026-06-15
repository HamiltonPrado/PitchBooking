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