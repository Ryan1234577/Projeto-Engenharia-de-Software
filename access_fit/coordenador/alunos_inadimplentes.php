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


$query_inadimplentes = $conn->query("
    SELECT p.id as transacao_id, u.nome, a.cpf, p.valor, p.mes_referencia, p.status, a.id as aluno_id
    FROM pagamentos p
    INNER JOIN alunos a ON p.aluno_id = a.id
    INNER JOIN usuarios u ON a.usuario_id = u.id
    WHERE (p.status = 'pendente' OR p.status = 'atrasado')
      AND p.id = (
          SELECT MAX(p2.id) 
          FROM pagamentos p2 
          WHERE p2.aluno_id = a.id
      )
    ORDER BY p.mes_referencia DESC, u.nome ASC
");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos Inadimplentes - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .navbar { background-color: #4e0000; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .navbar h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .back-link { color: #ffb3b3; text-decoration: none; font-weight: bold; border: 1px solid #ffb3b3; padding: 8px 15px; border-radius: 5px; transition: 0.3s; }
        .back-link:hover { background: #ffb3b3; color: #4e0000; }
        
        .main-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .section-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 5px solid #d10e0e; }
        .section-box h2 { color: #4e0000; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-size: 20px; }
        
        .table-responsive { width: 100%; overflow-x: auto; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #f8f9fa; padding: 12px; font-size: 14px; font-weight: bold; color: #495057; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; font-size: 14px; border-bottom: 1px solid #eee; color: #495057; }
        
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-pendente { background: #fff8e1; color: #f57f17; }
        .badge-atrasado { background: #f8d7da; color: #721c24; }
        
        .btn-action { background: #2e7d32; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; transition: 0.3s; }
        .btn-action:hover { background: #1b5e20; }
        .no-data { text-align: center; color: #6c757d; padding: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Access Fit <span style="font-weight: 300; font-size: 16px;">| Controle de Inadimplência</span></h1>
    <a href="dashboard_coordenador.php" class="back-link">Voltar ao Painel</a>
</div>

<div class="main-container">
    <div class="section-box">
        <h2>Alunos com Pendências Financeiras</h2>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Transação</th>
                        <th>Aluno</th>
                        <th>CPF</th>
                        <th>Mês de Referência</th>
                        <th>Valor Pendente</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_inadimplentes && $query_inadimplentes->num_rows > 0): ?>
                        <?php while ($row = $query_inadimplentes->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['transacao_id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['cpf']); ?></td>
                                <td><?php echo date('m/Y', strtotime($row['mes_referencia'] . '-01')); ?></td>
                                <td>R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['status'] === 'atrasado') ? 'badge-atrasado' : 'badge-pendente'; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="registrar_pagamento.php?aluno_id=<?php echo $row['aluno_id']; ?>" class="btn-action">Receber Pagamento</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data">Nenhum aluno inadimplente encontrado! Tudo em dia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>