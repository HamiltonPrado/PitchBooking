<?php
require 'db.php';

// Vai buscar os tipos de campo disponiveis e o preco
$resultado = mysqli_query($ligacao,
    "SELECT tipo_campo, MIN(preco_base) AS preco, MIN(custo_luz) AS luz, MIN(custo_material) AS material
     FROM campo
     WHERE estado = 'disponivel'
     GROUP BY tipo_campo");

