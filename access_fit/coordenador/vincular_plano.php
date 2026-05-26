<?php
session_start();

if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID do aluno não informado.");
}
$aluno_id = intval($_GET['id']);


$stmt_aluno = $conn->prepare("SELECT a.id AS aluno_id, u.nome FROM alunos a INNER JOIN usuarios u ON a.usuario_id = u.id WHERE a.id = ? LIMIT 1");
$stmt_aluno->bind_param("i", $aluno_id);
$stmt_aluno->execute();
$res_aluno = $stmt_aluno->get_result()->fetch_assoc();

if (!$res_aluno) {
    die("Aluno não encontrado no sistema.");
}


$col_nome_plano = verificarColuna($conn, 'planos', 'nome_plano') ? 'nome_plano' : 'nome';
$query_planos = $conn->query("SELECT id, $col_nome_plano as nome, valor FROM planos ORDER BY $col_nome_plano ASC");

function verificarColuna($conn, $tabela, $coluna) {
    $result = $conn->query("SHOW COLUMNS FROM `$tabela` LIKE '$coluna'");
    return ($result && $result->num_rows > 0);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular Plano - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .navbar { background-color: #4e0000; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .navbar h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .container { max-width: 500px; margin: 50px auto; padding: 0 20px; }
        .form-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 5px solid #4e0000; }
        .form-box h2 { color: #4e0000; margin-top: 0; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-size: 18px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: bold; color: #495057; margin-bottom: 5px; }
        select { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; box-sizing: border-box; color: #495057; }
        .btn-submit { background: #4e0000; color: white; border: none; padding: 12px 20px; border-radius: 5px; font-size: 14px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #d10e0e; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Access Fit <span style="font-weight: 300; font-size: 16px;">| Painel Coordenador</span></h1>
</div>

<div class="container">
    <div class="form-box">
        <h2>Vincular Plano ao Aluno</h2>
        <p style="font-size: 15px; color: #333; margin-bottom: 25px;">
            Aluno: <strong style="color: #4e0000; font-size: 16px;"><?php echo htmlspecialchars($res_aluno['nome']); ?></strong>
        </p>
        
        <form action="../actions/processa_vinculo.php" method="POST">
            <input type="hidden" name="aluno_id" value="<?php echo $res_aluno['aluno_id']; ?>">

            <div class="form-group">
                <label for="plano_id">Selecione o Plano da Academia</label>
                <select id="plano_id" name="plano_id" required>
                    <option value="">-- Selecione o Plano --</option>
                    <?php if ($query_planos && $query_planos->num_rows > 0): ?>
                        <?php while ($plano = $query_planos->fetch_assoc()): ?>
                            <option value="<?php echo $plano['id']; ?>">
                                <?php echo htmlspecialchars($plano['nome']); ?> - R$ <?php echo number_format($plano['valor'], 2, ',', '.'); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <button type="submit" class="btn-submit">CONFIRMAR VÍNCULO</button>
            <a href="dashboard_coordenador.php" class="back-link">Cancelar</a>
        </form>
    </div>
</div>

</body>
</html>