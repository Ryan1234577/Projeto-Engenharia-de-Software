<?php
session_start();
include('../config/config.php');


if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}


if (!isset($_GET['id'])) {
    die("ID não fornecido.");
}

$id_usuario = $_GET['id'];


$sql = "SELECT u.id as user_id, u.nome, u.email, a.id as aluno_id, a.cpf, a.telefone, a.data_nascimento, a.endereco, a.historico_saude 
        FROM usuarios u 
        LEFT JOIN alunos a ON a.usuario_id = u.id 
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$dados = $resultado->fetch_assoc();

if (!$dados) {
    die("Usuário ou Aluno não encontrado no sistema.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : $dados['cpf'];
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : $dados['telefone'];
    $data_nasc = isset($_POST['data_nascimento']) ? $_POST['data_nascimento'] : $dados['data_nascimento'];
    $endereco = isset($_POST['endereco']) ? $_POST['endereco'] : $dados['endereco'];
    $historico_saude = isset($_POST['historico_saude']) ? $_POST['historico_saude'] : $dados['historico_saude'];

    $conn->begin_transaction();

    try {
        
        $sql_u = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
        $stmt_u = $conn->prepare($sql_u);
        $stmt_u->bind_param("ssi", $nome, $email, $id_usuario);
        
        if (!$stmt_u->execute()) {
            if ($conn->errno == 1062) {
                throw new Exception("Este e-mail já está cadastrado no sistema.");
            }
            throw new Exception($stmt_u->error);
        }

        
        if (!empty($dados['aluno_id'])) {
            $sql_a = "UPDATE alunos SET nome = ?, cpf = ?, telefone = ?, data_nascimento = ?, endereco = ?, historico_saude = ? WHERE usuario_id = ?";
            $stmt_a = $conn->prepare($sql_a);
            $stmt_a->bind_param("ssssssi", $nome, $cpf, $telefone, $data_nasc, $endereco, $historico_saude, $id_usuario);
            
            if (!$stmt_a->execute()) {
                if ($conn->errno == 1062) {
                    throw new Exception("Dados duplicados! O CPF ou Telefone inserido já pertence a outro aluno.");
                }
                throw new Exception($stmt_a->error);
            }
        }

        $conn->commit();
        echo "<script>
                alert('Alterações salvas com sucesso!');
                window.location.href = 'dashboard_coordenador.php';
              </script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $erro = "Erro ao salvar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 450px; border-top: 5px solid #4e0000; }
        h2 { color: #4e0000; margin-top: 0; font-size: 22px; text-align: center; }
        .group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; font-size: 14px; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; font-size: 14px; font-family: inherit; }
        textarea { resize: vertical; height: 80px; }
        .btn-submit { background: #4e0000; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; cursor: pointer; font-weight: bold; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: #d10e0e; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #777; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Editar Perfil</h2>
    
    <?php if(isset($erro)) echo "<p style='color:red; text-align:center;'>$erro</p>"; ?>

    <form method="POST">
        <div class="group">
            <label>Nome Completo</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($dados['nome']); ?>" required>
        </div>

        <div class="group">
            <label>E-mail</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($dados['email']); ?>" required>
        </div>

        <?php if (!empty($dados['aluno_id'])): ?>
            <div class="group">
                <label>CPF</label>
                <input type="text" name="cpf" value="<?php echo htmlspecialchars($dados['cpf']); ?>">
            </div>

            <div class="group">
                <label>Telefone</label>
                <input type="text" name="telefone" value="<?php echo htmlspecialchars($dados['telefone']); ?>">
            </div>

            <div class="group">
                <label>Data de Nascimento</label>
                <input type="date" name="data_nascimento" value="<?php echo $dados['data_nascimento']; ?>">
            </div>

            <div class="group">
                <label>Endereço</label>
                <input type="text" name="endereco" value="<?php echo htmlspecialchars($dados['endereco']); ?>">
            </div>

            <div class="group">
                <label>Histórico de Saúde</label>
                <textarea name="historico_saude"><?php echo htmlspecialchars($dados['historico_saude']); ?></textarea>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn-submit">SALVAR ALTERAÇÕES</button>
        <a href="dashboard_coordenador.php" class="back-link">Voltar para o Painel</a>
    </form>
</div>

</body>
</html>