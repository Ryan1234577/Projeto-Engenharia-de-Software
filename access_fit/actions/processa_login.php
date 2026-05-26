<?php
session_start();


if (file_exists('../config/config.php')) {
    include('../config/config.php');
} else {
    die("Erro do sistema: O arquivo de configuração não foi encontrado no caminho correto.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string(trim($_POST['email']));
    $senha = $_POST['senha'];

    
    $sql = "SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome']       = $usuario['nome'];
            $_SESSION['email']      = $usuario['email'];
            $_SESSION['perfil']     = $usuario['perfil'];

            
            if ($usuario['perfil'] === 'coordenador') {
                header("Location: ../coordenador/dashboard_coordenador.php");
            } elseif ($usuario['perfil'] === 'instrutor') {
                header("Location: ../instrutor/dashboard_instrutor.php");
            } else {
                header("Location: ../aluno/dashboard.php");
            }
            exit();
        } else {
            echo "<script>
                    alert('Senha incorreta!');
                    window.history.back();
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('E-mail não cadastrado!');
                window.history.back();
              </script>";
        exit();
    }
}
?>