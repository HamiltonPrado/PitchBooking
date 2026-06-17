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

else if ($acao == 'criar') {

    // Só gestor pode criar campos
    if ($_SESSION['role'] != 'gestor') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    $identificador = $_POST['identificador'];
    $tipo_campo = $_POST['tipo_campo'];
    $descricao = $_POST['descricao'];
    $preco_base = $_POST['preco_base'];
    $custo_luz = $_POST['custo_luz'];
    $custo_material = $_POST['custo_material'];

    $stmt = mysqli_prepare($ligacao,
        "INSERT INTO campo (identificador, tipo_campo, descricao, preco_base, custo_luz, custo_material)
         VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssddd",
        $identificador, $tipo_campo, $descricao, $preco_base, $custo_luz, $custo_material);
    mysqli_stmt_execute($stmt);

    echo json_encode(['ok' => 'Campo criado']);
}
else if ($acao == 'editar') {

    // Só gestor pode editar campos
    if ($_SESSION['role'] != 'gestor') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    $id = $_POST['id'];
    $preco_base = $_POST['preco_base'];
    $custo_luz = $_POST['custo_luz'];
    $custo_material = $_POST['custo_material'];
    $estado = $_POST['estado'];

    $stmt = mysqli_prepare($ligacao,
        "UPDATE campo SET preco_base = ?, custo_luz = ?, custo_material = ?, estado = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "dddsi",
        $preco_base, $custo_luz, $custo_material, $estado, $id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['ok' => 'Campo atualizado']);
}
else if ($acao == 'apagar') {

    // Só gestor pode apagar campos
    if ($_SESSION['role'] != 'gestor') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    $id = $_POST['id'];

    $stmt = mysqli_prepare($ligacao, "DELETE FROM campo WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['ok' => 'Campo apagado']);
}