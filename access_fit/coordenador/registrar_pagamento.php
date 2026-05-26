<?php
session_start();

if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');


if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}


$query_alunos = $conn->query("SELECT a.id, u.nome, a.cpf, a.status FROM alunos a INNER JOIN usuarios u ON a.usuario_id = u.id ORDER BY u.nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Pagamento - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .navbar { background-color: #4e0000; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .navbar h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .back-link { color: #ffb3b3; text-decoration: none; font-weight: bold; border: 1px solid #ffb3b3; padding: 8px 15px; border-radius: 5px; transition: 0.3s; }
        .back-link:hover { background: #ffb3b3; color: #4e0000; }
        
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .form-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 5px solid #4e0000; }
        .form-box h2 { color: #4e0000; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-size: 20px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #495057; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        .form-control:focus { border-color: #4e0000; outline: none; }
        
        .btn-submit { background: #4e0000; color: white; padding: 12px 20px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #d10e0e; }
        
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; font-weight: bold; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Access Fit <span style="font-weight: 300; font-size: 16px;">| Registrar Pagamento</span></h1>
    <a href="dashboard_coordenador.php" class="back-link">Voltar ao Painel</a>
</div>

<div class="container">
    <div class="form-box">
        <h2>Lançamento Manual de Pagamento</h2>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'erro'): ?>
            <div class="alert alert-error">Erro ao registrar pagamento. Verifique os dados e tente novamente.</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sucesso'): ?>
            <div class="alert alert-success">Pagamento registrado com sucesso! O status do aluno foi atualizado.</div>
        <?php endif; ?>

        <form action="../actions/processa_pagamento.php" method="POST">
            
            <div class="form-group">
                <label for="aluno_id">Selecionar Aluno:</label>
                <select name="aluno_id" id="aluno_id" class="form-control" required>
                    <option value="">-- Escolha um aluno --</option>
                    <?php if ($query_alunos && $query_alunos->num_rows > 0): ?>
                        <?php while ($aluno = $query_alunos->fetch_assoc()): ?>
                            <?php 
                            $status_atual = strtolower(trim($aluno['status']));
                            $sufixo_status = ($status_atual === 'ativo') ? ' [Ativo]' : ' [INADIMPLENTE]'; 
                            $selected = (isset($_GET['aluno_id']) && $_GET['aluno_id'] == $aluno['id']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $aluno['id']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($aluno['nome']); ?> (CPF: <?php echo htmlspecialchars($aluno['cpf']); ?>)<?php echo $sufixo_status; ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="plano_id">Plano Referente:</label>
                <select name="plano_id" id="plano_id" class="form-control" required>
                    <option value="">-- Escolha o plano correspondente --</option>
                    <?php
                    $query_planos = $conn->query("SELECT id, nome, valor FROM planos ORDER BY nome ASC");
                    if ($query_planos && $query_planos->num_rows > 0) {
                        while ($plano = $query_planos->fetch_assoc()) {
                            $valor_formatado = number_format($plano['valor'], 2, ',', '.');
                            echo "<option value='{$plano['id']}'>" . htmlspecialchars($plano['nome']) . " (R$ {$valor_formatado})</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="valor">Valor Pago (R$):</label>
                <input type="number" step="0.01" name="valor" id="valor" class="form-control" placeholder="Ex: 90.00" required>
            </div>

            <div class="form-group">
                <label for="data_pagamento">Data do Lançamento:</label>
                <input type="date" name="data_pagamento" id="data_pagamento" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label for="forma_pagamento">Forma de Pagamento:</label>
                <select name="forma_pagamento" id="forma_pagamento" class="form-control" required>
                    <option value="">-- Selecione a forma --</option>
                    <option value="cartao">Cartão</option>
                    <option value="pix">Pix</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status do Pagamento:</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="pago">Pago / Aprovado (Ativa o Aluno)</option>
                    <option value="pendente">Pendente</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Confirmar e Salvar Transação</button>
        </form>
    </div>
</div>

</body>
</html>