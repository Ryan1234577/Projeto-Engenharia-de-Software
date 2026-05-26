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


$query_faturamento = $conn->query("
    SELECT mes_referencia, SUM(valor) as total_mes, COUNT(id) as total_transacoes 
    FROM pagamentos 
    WHERE status = 'em dia' OR status = 'pago' OR status = 'aprovado'
    GROUP BY mes_referencia 
    ORDER BY mes_referencia DESC
");


$query_status_alunos = $conn->query("
    SELECT status, COUNT(*) as total 
    FROM alunos 
    GROUP BY status
");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios Gerenciais - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; color: #333; }
        .navbar { background-color: #4e0000; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .navbar h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        
        .btn-nav { text-decoration: none; font-weight: bold; padding: 8px 15px; border-radius: 5px; transition: 0.3s; font-size: 14px; }
        .btn-back { color: #ffb3b3; border: 1px solid #ffb3b3; margin-right: 10px; }
        .btn-back:hover { background: #ffb3b3; color: #4e0000; }
        .btn-print { background: #2e7d32; color: white; border: none; cursor: pointer; }
        .btn-print:hover { background: #1b5e20; }
        
        .main-container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .report-header { text-align: center; margin-bottom: 30px; border-bottom: 2px dashed #ccc; padding-bottom: 20px; }
        .report-header h2 { margin: 5px 0; color: #4e0000; }
        
        .grid-relatorios { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; }
        .report-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .report-box h3 { color: #4e0000; margin-top: 0; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-size: 18px; }
        
        table { width: 100%; border-collapse: collapse; text-align: left; margin-top: 15px; }
        th { background: #f8f9fa; padding: 12px; font-size: 14px; font-weight: bold; color: #495057; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; font-size: 14px; border-bottom: 1px solid #eee; color: #495057; }
        .total-row { font-weight: bold; background-color: #f1f3f5; }

        /* Estilização para quando for mandar imprimir (Ctrl + P) */
        @media print {
            body { background-color: white; }
            .navbar, .btn-print, .btn-back { display: none !important; }
            .main-container { max-width: 100%; margin: 0; padding: 0; }
            .report-box { box-shadow: none; padding: 10px; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Access Fit <span style="font-weight: 300; font-size: 16px;">| Central de Relatórios</span></h1>
    <div>
        <a href="dashboard_coordenador.php" class="btn-nav btn-back">Voltar ao Painel</a>
        <button onclick="window.print();" class="btn-nav btn-print"> Imprimir Relatório</button>
    </div>
</div>

<div class="main-container">
    
    <div class="report-header">
        <h2>Access Fit - Relatório Gerencial de Desempenho</h2>
        <p>Gerado em: <?php echo date('d/m/Y H:i'); ?> | Perfil: Coordenador</p>
    </div>

    <div class="grid-relatorios">
        
        <div class="report-box">
            <h3> Faturamento Mensal (Fluxo de Caixa)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Mês de Referência</th>
                        <th>Qtd. Transações</th>
                        <th>Total Arrecadado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $geral_arrecadado = 0;
                    if ($query_faturamento && $query_faturamento->num_rows > 0): 
                    ?>
                        <?php while ($fat = $query_faturamento->fetch_assoc()): 
                            $geral_arrecadado += $fat['total_mes'];
                            // Formata a exibição do mês (ex: 2026-05 vira 05/2026)
                            $mes_formatado = implode('/', array_reverse(explode('-', $fat['mes_referencia'])));
                        ?>
                            <tr>
                                <td><strong><?php echo $mes_formatado; ?></strong></td>
                                <td><?php echo $fat['total_transacoes']; ?> pagamentos</td>
                                <td style="color: #2e7d32; font-weight: bold;">R$ <?php echo number_format($fat['total_mes'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <tr class="total-row">
                            <td colspan="2">Faturamento Total Acumulado</td>
                            <td style="color: #1b5e20;">R$ <?php echo number_format($geral_arrecadado, 2, ',', '.'); ?></td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="3">Nenhum histórico de faturamento encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="report-box">
            <h3> Distribuição de Alunos por Situação</h3>
            <table>
                <thead>
                    <tr>
                        <th>Status do Aluno</th>
                        <th>Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_alunos_sistema = 0;
                    if ($query_status_alunos && $query_status_alunos->num_rows > 0): 
                    ?>
                        <?php while ($row = $query_status_alunos->fetch_assoc()): 
                            $total_alunos_sistema += $row['total'];
                        ?>
                            <tr>
                                <td style="text-transform: capitalize;">
                                    <strong><?php echo htmlspecialchars($row['status']); ?></strong>
                                </td>
                                <td><?php echo $row['total']; ?> alunos</td>
                            </tr>
                        <?php endwhile; ?>
                        <tr class="total-row">
                            <td>Total Geral de Matrículas</td>
                            <td><?php echo $total_alunos_sistema; ?> alunos cadastrados</td>
                        </tr>
                    <?php else: ?>
                        <tr><td colspan="2">Nenhum aluno registrado para métricas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 30px; padding: 15px; background: #e8f5e9; border-radius: 6px; font-size: 13px; line-height: 1.5; color: #1b5e20;">
                <strong> Dica do Sistema:</strong> Use o botão <strong>Imprimir Relatório</strong> no topo da página para salvar este documento diretamente em formato PDF ou enviar para a sua impressora física corporativa de forma limpa.
            </div>
        </div>

    </div>

</div>

</body>
</html>