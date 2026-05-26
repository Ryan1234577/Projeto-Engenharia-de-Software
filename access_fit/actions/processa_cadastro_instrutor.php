<?php
if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome          = trim($_POST['nome']);
    $email         = strtolower(trim($_POST['email']));
    $cref          = trim($_POST['cref']);
    $telefone      = trim($_POST['telefone']);
    $especialidade = $_POST['especialidade'];
    $senha_bruta   = $_POST['senha'];
    $senha_hash    = password_hash($senha_bruta, PASSWORD_DEFAULT);

    $foto_url      = !empty($_POST['foto_url']) ? trim($_POST['foto_url']) : 'https://cdn-icons-png.flaticon.com/512/149/149071.png';

    $conn->begin_transaction();

    try {
        $stmt1 = $conn->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, 'instrutor')");
        $stmt1->bind_param("sss", $nome, $email, $senha_hash);
        
        if (!$stmt1->execute()) {
            if ($conn->errno == 1062) {
                throw new Exception("O e-mail informado já está cadastrado para outro usuário.");
            }
            throw new Exception($stmt1->error);
        }

        $usuario_id = $conn->insert_id;
        $status_inicial = 'ativo';
        $data_hoje      = date('Y-m-d');
        
        $stmt2 = $conn->prepare("INSERT INTO instrutores (usuario_id, nome, especialidade, cref, data_contratacao, status, telefone, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("isssssss", $usuario_id, $nome, $especialidade, $cref, $data_hoje, $status_inicial, $telefone, $foto_url);
        
        if (!$stmt2->execute()) {
            if ($conn->errno == 1062) {
                throw new Exception("Dados duplicados detectados! O CREF ou Telefone já pertencem a outro instrutor.");
            }
            throw new Exception($stmt2->error);
        }

        $conn->commit();

        echo "<script>
                alert('Instrutor cadastrado com sucesso!'); 
                window.location.href='../academia_instrutor.php';
              </script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>
                alert('Erro ao cadastrar instrutor: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: ../academia_instrutor.php");
    exit();
}
?>