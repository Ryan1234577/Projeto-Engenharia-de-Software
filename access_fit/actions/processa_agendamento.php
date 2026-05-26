<?php
session_start();
include('../config/config.php');

$turma_id = isset($_POST['turma_id']) ? intval($_POST['turma_id']) : 0;
$acao = isset($_POST['acao']) ? $_POST['acao'] : '';
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;

$stmt_aluno = $conn->prepare("SELECT id FROM alunos WHERE usuario_id = ?");
$stmt_aluno->bind_param("i", $usuario_id);
$stmt_aluno->execute();
$res_aluno = $stmt_aluno->get_result()->fetch_assoc();
$aluno_id = $res_aluno ? $res_aluno['id'] : 0;

if ($turma_id === 0 || $aluno_id === 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos ou sessão expirada.']);
    exit();
}

if ($acao === 'agendar') {
    $stmt_ins = $conn->prepare("INSERT INTO agendamentos (aluno_id, turma_id, data_aula, status) VALUES (?, ?, NOW(), 'confirmado')");
    $stmt_ins->bind_param("ii", $aluno_id, $turma_id);
    if ($stmt_ins->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao agendar aula.']);
    }
} elseif ($acao === 'cancelar') {
    $stmt_del = $conn->prepare("DELETE FROM agendamentos WHERE aluno_id = ? AND turma_id = ? AND status = 'confirmado'");
    $stmt_del->bind_param("ii", $aluno_id, $turma_id);
    if ($stmt_del->execute()) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cancelar agendamento.']);
    }
}