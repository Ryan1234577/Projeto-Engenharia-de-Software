<?php
require_once('../config/config.php');
session_start();

// Segurança: impede acesso de quem não é coordenador
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'coordenador') {
    die("Acesso negado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Coleta TODOS os dados do formulário
    $nome     = $_POST['nome'];
    $email    = $_POST['email'];
    $senha    = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $cpf      = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $data_nasc = $_POST['data_nasc'];
    $endereco = $_POST['endereco'];
    $saude    = $_POST['historico_saude'];
    $perfil   = 'aluno';
    $status   = 'ativo';

   
    $conn->begin_transaction();

    try {
        $sql_user = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("ssss", $nome, $email, $senha, $perfil);
        $stmt_user->execute();


        $usuario_id = $conn->insert_id;

        $sql_aluno = "INSERT INTO alunos (usuario_id, nome, cpf, data_nascimento, telefone, endereco, historico_saude, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_aluno = $conn->prepare($sql_aluno);
        
        $stmt_aluno->bind_param("isssssss", 
            $usuario_id, 
            $nome, 
            $cpf, 
            $data_nasc, 
            $telefone, 
            $endereco, 
            $saude, 
            $status
        );
        
        $stmt_aluno->execute();

        
        $conn->commit();

        echo "<script>
                alert('Sucesso: Aluno e Usuário cadastrados por completo!');
                window.location.href = '../coordenador/dashboard_coordenador.php';
              </script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
                alert('Erro crítico: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
}
?>