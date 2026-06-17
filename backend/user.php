<?php
session_start();
if (!isset($_SESSION['atleta_id'])) {
    header('Location: ../frontend/login.html');
    exit;
}
require 'db.php';
header('Content-Type: application/json');

$acao = $_POST['acao'] ?? $_GET['acao'] ?? 'perfil';

if ($acao == 'perfil') {

$atleta_id = $_SESSION['atleta_id'];

// Vai buscar os dados do atleta e o estado dos documentos
$stmt = mysqli_prepare($ligacao,
"SELECT nome, email, tipo_doc, num_doc, nif, docs_verificados, role FROM atleta WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $atleta_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$dados = mysqli_fetch_assoc($resultado);

// Vai buscar as reservas do atleta, com o tipo e identificador do campo
$stmt = mysqli_prepare($ligacao,
    "SELECT r.id, c.tipo_campo, c.identificador, r.data_jogo, r.hora_inicio, r.hora_fim,
            r.valor_total, r.estado, r.check_in
     FROM reserva r
     JOIN campo c ON c.id = r.campo_id
     WHERE r.atleta_id = ?
     ORDER BY r.data_jogo DESC");
mysqli_stmt_bind_param($stmt, "i", $atleta_id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$reservas = [];
while ($linha = mysqli_fetch_assoc($resultado)) {
    $reservas[] = $linha;
}

// Devolve dados do atleta e reservas num so JSON
echo json_encode([
    "dados" => $dados,
    "reservas" => $reservas
]);
}

else if ($acao == 'listar_atletas') {

    // Só staff pode listar atletas
    if ($_SESSION['role'] != 'gestor' && $_SESSION['role'] != 'rececionista') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    // Lista todos os atletas (clientes)
    $resultado = mysqli_query($ligacao,
    "SELECT id, nome, email, tipo_doc, num_doc, nif, estado, docs_verificados, role
     FROM atleta
     WHERE role = 'cliente'
     ORDER BY nome");

    $atletas = [];
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $atletas[] = $linha;
    }

    echo json_encode($atletas);
}

else if ($acao == 'verificar_docs') {

    // Só staff pode verificar documentos
    if ($_SESSION['role'] != 'gestor' && $_SESSION['role'] != 'rececionista') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    $alvo_id = $_POST['atleta_id'];

    $stmt = mysqli_prepare($ligacao,
        "UPDATE atleta SET docs_verificados = 1 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $alvo_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['ok' => 'Documentos verificados']);
}
else if ($acao == 'alterar_estado') {

    // Só staff pode ativar/inativar atletas
    if ($_SESSION['role'] != 'gestor' && $_SESSION['role'] != 'rececionista') {
        echo json_encode(['erro' => 'Sem permissão']);
        exit;
    }

    $alvo_id = $_POST['atleta_id'];
    $novo_estado = $_POST['estado'];

    $stmt = mysqli_prepare($ligacao,
        "UPDATE atleta SET estado = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $novo_estado, $alvo_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['ok' => 'Estado alterado']);
}