<?php
require 'db.php';
header('Content-Type: application/json');

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