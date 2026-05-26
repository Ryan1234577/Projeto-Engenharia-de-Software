<?php
session_start();

// AJUSTADO: Caminho do arquivo de configuração
if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
require_once('../config/config.php');

// CORRIGIDO: Agora valida corretamente o Coordenador (Dono da UC_06)
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $valor = $_POST['valor'];
    $periodo = $_POST['periodo'];

    if (!empty($nome) && !empty($valor)) {
        $stmt = $conn->prepare("INSERT INTO planos (nome, valor, periodo) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $nome, $valor, $periodo);

        if ($stmt->execute()) {
            // CORRIGIDO: Retorna o coordenador para o painel dele após salvar no banco
            echo "<script>alert('Plano cadastrado com sucesso!'); window.location.href='../coordenador/dashboard_coordenador.php';</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar.'); window.history.back();</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>