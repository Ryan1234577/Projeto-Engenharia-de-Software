<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (file_exists('../config/config.php')) {
    include('../config/config.php');
} else {
    $conn = new mysqli("localhost", "root", "", "access_fit");
}


if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['usuario_id']; 
    $nome_aluno = $_SESSION['usuario_nome'] ?? 'Aluno'; 
    $cpf        = $_POST['cpf'];
    $data_nasc  = $_POST['data_nascimento'];
    $telefone   = $_POST['telefone'];
    $endereco   = $_POST['endereco'];
    $saude      = $_POST['historico_saude'];

    
    $sql = "INSERT INTO alunos (usuario_id, nome, cpf, data_nascimento, telefone, endereco, historico_saude, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'ativo')";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("Erro no banco: " . $conn->error);
    }

    $stmt->bind_param("issssss", $usuario_id, $nome_aluno, $cpf, $data_nasc, $telefone, $endereco, $saude);

    if ($stmt->execute()) {
        echo "<script>
                alert('Perfil ativado com sucesso!');
                window.location.href = 'dashboard.php'; 
              </script>";
        exit();
    } else {
        echo "Erro ao salvar: " . $conn->error;
    }
}

$nome_exibicao = $_SESSION['usuario_nome'] ?? 'Aluno';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Completar Cadastro - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { color: #4e0000; text-align: center; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input, textarea { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 15px; background: #4e0000; color: white; border: none; border-radius: 6px; margin-top: 25px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
<div class="card">
    <h2>Olá, <?php echo htmlspecialchars($nome_exibicao); ?>!</h2>
    <form method="POST">
        <label>CPF</label>
        <input type="text" name="cpf" required>
        <label>Data de Nascimento</label>
        <input type="date" name="data_nascimento" required>
        <label>Telefone</label>
        <input type="text" name="telefone" required>
        <label>Endereço</label>
        <input type="text" name="endereco" required>
        <label>Saúde/Alergias</label>
        <textarea name="historico_saude"></textarea>
        <button type="submit">Finalizar e Acessar Painel</button>
    </form>
</div>
</body>
</html>