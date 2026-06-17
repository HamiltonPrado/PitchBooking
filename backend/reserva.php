<?php
session_start();
if (!isset($_SESSION['atleta_id'])) {
    header('Location: ../frontend/login.html');
    exit;
}
require 'db.php';
$acao = $_POST['acao'] ?? $_GET['acao'] ?? 'criar';

if ($acao == 'criar') {

$atleta_id = $_SESSION['atleta_id'];
$tipo_campo = $_POST['tipo_campo'];
$data_jogo = $_POST['data_jogo'];
$hora_inicio = $_POST['hora_inicio'];
$hora_fim = $_POST['hora_fim'];
$usa_luz = isset($_POST['usa_luz']) ? 1 : 0;
$qtd_material = $_POST['qtd_material'];

 // Validação: hora de fim tem de ser depois da hora de início
    if ($hora_fim <= $hora_inicio) {
        die('A hora de fim tem de ser depois da hora de início.');
    }

    // Validação: não permitir reservas em datas/horas passadas
    $data_hora_reserva = $data_jogo . ' ' . $hora_inicio;
    if (strtotime($data_hora_reserva) < time()) {
        die('Não é possível reservar para uma data ou hora que já passou.');
    }

// Encontra um campo desse tipo que esteja livre no horario pedido
$stmt = mysqli_prepare($ligacao,
    "SELECT id FROM campo
     WHERE tipo_campo = ?
       AND estado = 'disponivel'
       AND id NOT IN (
           SELECT campo_id FROM reserva
           WHERE data_jogo = ? AND hora_inicio = ? AND estado = 'ativa'
       )
     LIMIT 1");
mysqli_stmt_bind_param($stmt, "sss", $tipo_campo, $data_jogo, $hora_inicio);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$campo_livre = mysqli_fetch_assoc($resultado);

// Se nao houver nenhum campo livre desse tipo, avisa
if (!$campo_livre) {
    die('Não existem campos disponíveis deste tipo para a data e horário escolhidos.');
}

$campo_id = $campo_livre['id']; 

// Vai buscar os precos do campo escolhido
$stmt = mysqli_prepare($ligacao,
    "SELECT preco_base, custo_luz, custo_material FROM campo WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $campo_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$campo = mysqli_fetch_assoc($resultado);

// Calcula o total: base + luz (se ativada) + material (por unidade)
$valor_total = $campo['preco_base'];
if ($usa_luz) {
    $valor_total = $valor_total + $campo['custo_luz'];
}
$valor_total = $valor_total + ($campo['custo_material'] * $qtd_material);

// Insere a reserva na base de dados
$stmt = mysqli_prepare($ligacao,
    "INSERT INTO reserva (atleta_id, campo_id, data_jogo, hora_inicio, hora_fim, usa_luz, qtd_material, valor_total)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "iisssiid",
    $atleta_id, $campo_id, $data_jogo, $hora_inicio, $hora_fim, $usa_luz, $qtd_material, $valor_total);
mysqli_stmt_execute($stmt);

header('Location: ../frontend/perfil.html');
exit;
}

else if ($acao == 'listar') {

    header('Content-Type: application/json');

    // Só staff (gestor ou rececionista) pode listar todas as reservas
    if ($_SESSION['role'] != 'gestor' && $_SESSION['role'] != 'rececionista') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    // Vai buscar todas as reservas do clube, com atleta e campo
    $resultado = mysqli_query($ligacao,
        "SELECT r.id, a.nome AS atleta, c.tipo_campo, c.identificador,
                r.data_jogo, r.hora_inicio, r.hora_fim, r.valor_total, r.estado, r.check_in
         FROM reserva r
         JOIN atleta a ON a.id = r.atleta_id
         JOIN campo c ON c.id = r.campo_id
         ORDER BY r.data_jogo DESC");

    $reservas = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $reservas[] = $linha;
    }

    echo json_encode($reservas);
}


else if ($acao == 'cancelar') {

    // Só staff pode cancelar reservas pelo backoffice

    if ($_SESSION['role'] != 'gestor' && $_SESSION['role'] != 'rececionista') {

        die('Sem permissão.');

    }

    $reserva_id = $_POST['reserva_id'];

    $stmt = mysqli_prepare($ligacao,

        "UPDATE reserva SET estado = 'cancelada' WHERE id = ?");

    mysqli_stmt_bind_param($stmt, "i", $reserva_id);

    mysqli_stmt_execute($stmt);

    echo 'Reserva cancelada.';

}

else if ($acao == 'checkin') {

    // Só staff pode fazer check-in

    if ($_SESSION['role'] != 'gestor' && $_SESSION['role'] != 'rececionista') {

        die('Sem permissão.');

    }

    $reserva_id = $_POST['reserva_id'];

    $stmt = mysqli_prepare($ligacao,

        "UPDATE reserva SET check_in = 1 WHERE id = ?");

    mysqli_stmt_bind_param($stmt, "i", $reserva_id);

    mysqli_stmt_execute($stmt);

    echo 'Check-in efetuado.';

}

else if ($acao == 'relatorios') {

    header('Content-Type: application/json');

    // Só staff pode ver relatorios
    if ($_SESSION['role'] != 'gestor' && $_SESSION['role'] != 'rececionista') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    // 1. Estado das reservas (quantas ativas / canceladas)
    $estado = mysqli_query($ligacao,
        "SELECT estado, COUNT(*) AS total FROM reserva GROUP BY estado");
    $por_estado = [];
    while ($linha = mysqli_fetch_assoc($estado)) {
        $por_estado[] = $linha;
    }

    // 2. Receita por modalidade (soma dos pagamentos por tipo de campo)
    $receita = mysqli_query($ligacao,
        "SELECT c.tipo_campo, SUM(p.montante) AS total
         FROM pagamento p
         JOIN reserva r ON r.id = p.reserva_id
         JOIN campo c ON c.id = r.campo_id
         GROUP BY c.tipo_campo");
    $por_modalidade = [];
    while ($linha = mysqli_fetch_assoc($receita)) {
        $por_modalidade[] = $linha;
    }

    // 3. Taxa de ocupacao (reservas ativas vs total de campos)
    $total_campos = mysqli_fetch_assoc(mysqli_query($ligacao,
        "SELECT COUNT(*) AS n FROM campo"))['n'];
    $reservas_ativas = mysqli_fetch_assoc(mysqli_query($ligacao,
        "SELECT COUNT(*) AS n FROM reserva WHERE estado = 'ativa'"))['n'];

    // Devolve os tres relatorios num so JSON
    echo json_encode([
        "por_estado" => $por_estado,
        "por_modalidade" => $por_modalidade,
        "total_campos" => $total_campos,
        "reservas_ativas" => $reservas_ativas
    ]);
}