<?php
session_start();

// AJUSTADO: Caminho do arquivo de configuração (subiu um nível)
if (!file_exists('../config/config.php')) {
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno de configuração.']);
    exit();
}
include('../config/config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$hoje = date('Y-m-d');

// 1. Capturar o ID da turma enviado pelo Dashboard (via GET)
$turma_id = isset($_GET['turma_id']) ? intval($_GET['turma_id']) : 0;

if ($turma_id === 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhuma turma selecionada.']);
    exit();
}

// 2. Pegar o ID real do aluno (tabela alunos)
$stmt_aluno = $conn->prepare("SELECT id FROM alunos WHERE usuario_id = ?");
$stmt_aluno->bind_param("i", $usuario_id);
$stmt_aluno->execute();
$res_aluno = $stmt_aluno->get_result()->fetch_assoc();

if (!$res_aluno) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Aluno não encontrado.']);
    exit();
}

$aluno_id = $res_aluno['id'];

// 3. Verificar se já existe check-in hoje NESTA TURMA ESPECÍFICA
$stmt_check = $conn->prepare("SELECT id FROM presencas WHERE aluno_id = ? AND data_presenca = ? AND turma_id = ?");
$stmt_check->bind_param("isi", $aluno_id, $hoje, $turma_id);
$stmt_check->execute();

if ($stmt_check->get_result()->num_rows > 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você já realizou o check-in nesta aula hoje!']);
    exit();
}

// 4. Inserir a presença dinâmica
$status_presente = 'sim';

$sql_insere = "INSERT INTO presencas (aluno_id, turma_id, data_presenca, presente) VALUES (?, ?, ?, ?)";
$stmt_insere = $conn->prepare($sql_insere);
$stmt_insere->bind_param("iiss", $aluno_id, $turma_id, $hoje, $status_presente);

if ($stmt_insere->execute()) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao registrar no banco: ' . $conn->error]);
}
?>