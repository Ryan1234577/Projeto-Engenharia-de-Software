<?php
session_start();

if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');

// Trava de segurança para o coordenador
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];

    // Coleta dos novos campos específicos do instrutor
    $especialidade = isset($_POST['especialidade']) ? trim($_POST['especialidade']) : '';
    $cref          = isset($_POST['cref']) ? trim($_POST['cref']) : '';
    $telefone      = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $foto_url      = (isset($_POST['foto_url']) && !empty($_POST['foto_url'])) ? trim($_POST['foto_url']) : null;
    
    // Valores padrão gerados pelo sistema para a tabela de instrutores
    $data_contratacao = date('Y-m-d');
    $status = 'ativo';

    if (empty($nome) || empty($email) || empty($senha) || empty($perfil)) {
        header("Location: ../coordenador/cadastrar_funcionario.php?msg=erro_geral");
        exit();
    }

    // 1. Verifica se o e-mail já existe no sistema para evitar duplicidade
    $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    
    if ($res_check->num_rows > 0) {
        header("Location: ../coordenador/cadastrar_funcionario.php?msg=erro_email");
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();

    // 2. Criptografa a senha usando MD5 (padrão mantido do projeto)
    $senha_cripto = md5($senha);

    // Inicia uma transação segura para garantir que salve em ambas ou em nenhuma
    $conn->begin_transaction();

    try {
        // 3. Insere primeiro na tabela geral de usuarios
        $stmt_user = $conn->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
        $stmt_user->bind_param("ssss", $nome, $email, $senha_cripto, $perfil);
        $stmt_user->execute();
        
        $novo_usuario_id = $conn->insert_id; // Pega o ID gerado para o usuário
        $stmt_user->close();

        // 4. Se o perfil for instrutor, popula a tabela específica com TODAS as colunas mapeadas
        if ($perfil === 'instrutor') {
            $sql_instrutor = "INSERT INTO instrutores (usuario_id, nome, especialidade, cref, telefone, foto_url, data_contratacao, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_inst = $conn->prepare($sql_instrutor);
            
            // Mapeamento: i (usuario_id) e 7 strings (s) para as demais colunas
            $stmt_inst->bind_param("isssssss", $novo_usuario_id, $nome, $especialidade, $cref, $telefone, $foto_url, $data_contratacao, $status);
            $stmt_inst->execute();
            $stmt_inst->close();
        }

        // Tudo ocorreu com sucesso, confirma gravação definitiva
        $conn->commit();
        header("Location: ../coordenador/cadastrar_funcionario.php?msg=sucesso");
        exit();

    } catch (Exception $e) {
        // Desfaz qualquer inserção parcial caso ocorra um erro de banco
        $conn->rollback();
        header("Location: ../coordenador/cadastrar_funcionario.php?msg=erro_geral");
        exit();
    }
} else {
    header("Location: ../coordenador/dashboard_coordenador.php");
    exit();
}
?>