<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$acao = $_POST['acao'] ?? $_GET['acao'] ?? 'listar';

if ($acao == 'listar') {

// Vai buscar os tipos de campo disponiveis e o preco
$resultado = mysqli_query($ligacao,
    "SELECT tipo_campo, MIN(preco_base) AS preco, MIN(custo_luz) AS luz, MIN(custo_material) AS material
     FROM campo
     WHERE estado = 'disponivel'
     GROUP BY tipo_campo");

// Junta os campos num array
$campos = [];
while ($campo = mysqli_fetch_assoc($resultado)) {
    $campos[] = $campo;
}

echo json_encode($campos);

}

else if ($acao == 'listar_todos') {

    // Só gestor pode ver e gerir os campos fisicos
    if ($_SESSION['role'] != 'gestor') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    // Lista todos os campos fisicos individuais
    $resultado = mysqli_query($ligacao,
        "SELECT id, identificador, tipo_campo, descricao, estado, preco_base, custo_luz, custo_material
         FROM campo
         ORDER BY identificador");

    $campos = [];
    while ($campo = mysqli_fetch_assoc($resultado)) {
        $campos[] = $campo;
    }

    echo json_encode($campos);
}