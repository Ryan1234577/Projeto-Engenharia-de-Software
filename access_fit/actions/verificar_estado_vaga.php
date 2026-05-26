<?php
session_start();
include('../config/config.php');

$turma_id = isset($_GET['turma_id']) ? intval($_GET['turma_id']) : 0;
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;

$stmt_aluno = $conn->prepare("SELECT id FROM alunos WHERE usuario_id = ?");
$stmt_aluno->bind_param("i", $usuario_id);
$stmt_aluno->execute();
$res_aluno = $stmt_aluno->get_result()->fetch_assoc();
$aluno_id = $res_aluno ? $res_aluno['id'] : 0;

if ($turma_id === 0 || $aluno_id === 0) {
    echo json_encode(['status' => 'nenhum']);
    exit();
}


$data_hoje = date('Y-m-d');
$stmt_p = $conn->prepare("SELECT id FROM presencas WHERE aluno_id = ? AND turma_id = ? AND DATE(data_presenca) = ?");
$stmt_p->bind_param("iis", $aluno_id, $turma_id, $data_hoje);
$stmt_p->execute();
if ($stmt_p->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'presenca']);
    exit();
}


$stmt_a = $conn->prepare("SELECT id FROM agendamentos WHERE aluno_id = ? AND turma_id = ? AND status = 'confirmado'");
$stmt_a->bind_param("ii", $aluno_id, $turma_id);
$stmt_a->execute();
if ($stmt_a->get_result()->num_rows > 0) {
    echo json_encode(['status' => 'agendado']);
    exit();
}

echo json_encode(['status' => 'nenhum']);