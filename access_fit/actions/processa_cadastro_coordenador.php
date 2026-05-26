<?php
session_start();
include('../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nome  = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
    $codigo_seguranca = isset($_POST['codigo_seguranca']) ? trim($_POST['codigo_seguranca']) : '';

    $chave_mestra = "ACCESSFIT123"; 

    if (strtoupper($codigo_seguranca) !== strtoupper($chave_mestra)) {
        echo "<script>
                alert('Código de Autenticação Administrativa incorreto! Cadastro negado.');
                window.location.href = '../coordenador/cadastro_coordenador.php';
              </script>";
        exit();
    }

    if (empty($nome) || empty($email) || empty($senha)) {
        echo "<script>
                alert('Erro: Todos os campos (Nome, E-mail e Senha) devem ser preenchidos!');
                window.history.back();
              </script>";
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $conn->begin_transaction();

    try {
        $sql_usuario = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, 'coordenador')";
        $stmt_user = $conn->prepare($sql_usuario);
        $stmt_user->bind_param("sss", $nome, $email, $senha_hash);
        
        if (!$stmt_user->execute()) {
            if ($conn->errno == 1062) {
                throw new Exception("Este e-mail já está cadastrado no sistema!");
            }
            throw new Exception($stmt_user->error);
        }
        
        $usuario_id = $conn->insert_id;
        $stmt_user->close();

        $sql_coordenador = "INSERT INTO coordenadores (usuario_id, nome, email, telefone) VALUES (?, ?, ?, ?)";
        $stmt_coord = $conn->prepare($sql_coordenador);
        $stmt_coord->bind_param("isss", $usuario_id, $nome, $email, $telefone); 
        
        if (!$stmt_coord->execute()) {
            if ($conn->errno == 1062) {
                throw new Exception("Dados duplicados! Este telefone ou e-mail já está registrado para um coordenador.");
            }
            throw new Exception($stmt_coord->error);
        }
        
        $stmt_coord->close();
        $conn->commit();

        echo "<script>
                alert('Coordenador cadastrado com sucesso! Prossiga com o login.');
                window.location.href = '../coordenador/login_coordenador.php';
              </script>";
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
                alert('Erro ao realizar cadastro: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
}
?>