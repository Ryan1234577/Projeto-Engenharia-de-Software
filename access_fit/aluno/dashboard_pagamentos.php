<?php
include('config.php');
$sql = "SELECT 
            p.data_pagamento, 
            u.nome AS aluno_nome, 
            pl.nome AS plano_nome, 
            p.mes_referencia, 
            p.valor, 
            p.status 
        FROM pagamentos p
        JOIN alunos a ON p.aluno_id = a.id
        JOIN usuarios u ON a.usuario_id = u.id
        LEFT JOIN planos pl ON p.plano_id = pl.id
        ORDER BY p.data_pagamento DESC";

$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão Financeira - Access Fit</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 1000px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #222; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .status-ok { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Relatório de Pagamentos</h1>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Aluno</th>
                <th>Plano</th>
                <th>Mês Ref.</th>
                <th>Valor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $res->fetch_assoc()): ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($row['data_pagamento'])); ?></td>
                <td><?php echo htmlspecialchars($row['aluno_nome']); ?></td>
                <td><?php echo htmlspecialchars($row['plano_nome'] ?? 'Plano não identificado'); ?></td>
                <td><?php echo $row['mes_referencia']; ?></td>
                <td>R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                <td class="status-ok"><?php echo strtoupper($row['status']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <br>
    <a href="dashboard_instrutor.php">Voltar</a>
</div>

</body>
</html>