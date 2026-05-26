<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome   = $_POST['nome'] ?? '';
    $email  = $_POST['email'] ?? '';
    $senha_plana = $_POST['senha'] ?? '';
    $perfil = $_POST['perfil'] ?? 'aluno'; 

    if (empty($nome) || empty($email) || empty($senha_plana)) {
        die("Erro: Preencha todos os campos do formulário.");
    }

    $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Erro na estrutura da tabela: " . $conn->error);
    }

    $stmt->bind_param("ssss", $nome, $email, $senha_hash, $perfil);

    if ($stmt->execute()) {
        $_SESSION['usuario_id']   = $conn->insert_id;
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['perfil']       = $perfil;

        echo "<script>
                alert('Usuário gravado com sucesso no banco!');
                window.location.href = '../aluno/completar_aluno.php';
              </script>";
        exit();
    } else {
        if ($conn->errno == 1062) {
            die("<script>alert('Erro: Este e-mail já está cadastrado no sistema!'); window.history.back();</script>");
        } else {
            die("O Banco de Dados recusou a gravação: " . $stmt->error);
        }
    }
}
?>