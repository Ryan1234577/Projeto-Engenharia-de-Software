<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. IMPORTAÇÃO DA CONEXÃO (Ajustado para o novo caminho)
if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');

// 2. VERIFICA SE OS DADOS FORAM ENVIADOS VIA POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Captura e limpa as entradas
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

    if (empty($email) || empty($senha)) {
        header("Location: ../academia.php?erro=campos_vazios");
        exit();
    }

    // Descobrir de qual página o usuário tentou fazer o login
    $veio_de = isset($_SERVER['HTTP_REFERER']) ? basename($_SERVER['HTTP_REFERER']) : '';

    // 3. BUSCA O USUÁRIO NO BANCO DE DADOS
    $stmt = $conn->prepare("SELECT id, nome, senha, perfil FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha']) || $senha === $usuario['senha']) {
            
            // Padroniza o perfil vindo do banco
            $perfil_usuario = strtolower(trim($usuario['perfil']));

            // 4. BLOQUEIOS DE ACESSO PELO PORTAL ERRADO
            
            // Se tentou logar pela página de INSTRUTOR mas não é instrutor
            if (strpos($veio_de, 'academia_instrutor.php') !== false && $perfil_usuario !== 'instrutor') {
                header("Location: ../academia_instrutor.php?erro=apenas_instrutores");
                exit();
            }

            // Se tentou logar pela página de ALUNO mas não é aluno
            if (strpos($veio_de, 'academia_instrutor.php') === false && $perfil_usuario !== 'aluno') {
                header("Location: ../academia.php?erro=apenas_alunos");
                exit();
            }

            // 5. SE PASSOU NAS REGRAS, CRIA A SESSÃO
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['perfil']       = $perfil_usuario;

            // 6. REDIRECIONAMENTO SEGURO COM BASE NO PERFIL (Ajustado caminhos das pastas)
            if ($perfil_usuario === 'instrutor') {
                header("Location: ../instrutor/dashboard_instrutor.php"); // Envia diretamente para dentro da pasta instrutor/
                exit();
            } else if ($perfil_usuario === 'aluno') {
                header("Location: ../aluno/dashboard.php"); // Envia diretamente para dentro da pasta aluno/
                exit();
            } else {
                header("Location: ../academia.php?erro=perfil_invalido");
                exit();
            }

        } else {
            redirecionarErro($veio_de);
        }
    } else {
        redirecionarErro($veio_de);
    }
} else {
    header("Location: ../academia.php");
    exit();
}

// Função auxiliar ajustada para o redirecionamento com caminhos relativos de pastas
function redirecionarErro($origem) {
    if (strpos($origem, 'academia_instrutor.php') !== false) {
        header("Location: ../academia_instrutor.php?erro=dados_invalidos");
    } else {
        header("Location: ../academia.php?erro=dados_invalidos");
    }
    exit();
}
?>