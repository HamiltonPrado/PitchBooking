<?php
require 'db.php';

// Vai buscar os tipos de campo disponiveis e o preco
$resultado = mysqli_query($ligacao,
    "SELECT tipo_campo, MIN(preco_base) AS preco, MIN(custo_luz) AS luz, MIN(custo_material) AS material
     FROM campo
     WHERE estado = 'disponivel'
     GROUP BY tipo_campo");

// Mostra cada tipo de campo
while ($campo = mysqli_fetch_assoc($resultado)) {
    echo '<h3>' . $campo['tipo_campo'] . '</h3>';
    echo 'Preço base: ' . $campo['preco'] . '€<br>';
    echo 'Suplemento luz: ' . $campo['luz'] . '€<br>';
    echo 'Aluguer material: ' . $campo['material'] . '€<br><hr>';
}