<?php
session_start();
require_once('config.php');
include('sidebar_coordenador.php');


if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'instrutor') {
    header("Location: academia_instrutor.php");
    exit();
}

$sql = "SELECT * FROM planos ORDER BY valor ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Dashboard de Planos - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #d10e0e; padding-bottom: 15px; margin-bottom: 25px; }
        .btn-add { background-color: #d10e0e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #333; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-mensal { background: #e3f2fd; color: #1976d2; }
        .badge-trimestral { background: #f3e5f5; color: #7b1fa2; }
        .badge-anual { background: #e8f5e9; color: #388e3c; }
        .acoes a { text-decoration: none; margin-right: 15px; font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Gestão de Planos</h1>
        <a href="cadastrar_plano.php" class="btn-add">+ Novo Plano</a>
    </header>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Plano</th>
                <th>Período</th>
                <th>Valor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['nome']); ?></strong></td>
                    <td><span class="badge badge-<?php echo strtolower($row['periodo']); ?>"><?php echo $row['periodo']; ?></span></td>
                    <td style="color: #d10e0e; font-weight: bold;">R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></td>
                    <td class="acoes">
                        <a href="editar_plano.php?id=<?php echo $row['id']; ?>" style="color: #0056b3;">Editar</a>
                        <a href="excluir_plano.php?id=<?php echo $row['id']; ?>" style="color: #d10e0e;" onclick="return confirm('Excluir este plano?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <br><a href="dashboard_instrutor.php" style="color: #666; text-decoration: none;">← Voltar ao Início</a>
</div>
</body>
</html>