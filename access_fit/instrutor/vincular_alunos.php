<?php
session_start();


if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');


if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'instrutor') {
    header("Location: ../academia_instrutor.php");
    exit();
}

$id_usuario_instrutor = $_SESSION['usuario_id'];


$res_inst = $conn->query("SELECT id FROM instrutores WHERE usuario_id = $id_usuario_instrutor");
if ($res_inst && $res_inst->num_rows > 0) {
    $instrutor = $res_inst->fetch_assoc();
    $id_instrutor_real = $instrutor['id'];
} else {
    
    $id_instrutor_real = $id_usuario_instrutor;
}


$sql = "SELECT id, nome FROM alunos WHERE instrutor_id IS NULL ORDER BY nome ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Vincular Novos Alunos - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #4e0000; border-bottom: 2px solid #cc0000; padding-bottom: 10px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .btn-adotar { background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 13px; display: inline-block; transition: 0.2s; }
        .btn-adotar:hover { background: #218838; }
        .voltar { display: inline-block; margin-bottom: 15px; color: #666; text-decoration: none; font-size: 14px; font-weight: 500; }
        .voltar:hover { color: #cc0000; }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard_instrutor.php" class="voltar">← Voltar para o Dashboard</a>
    
    <h2>Alunos Disponíveis para Vínculo</h2>
    <p style="color: #666; font-size: 14px;">Selecione um aluno abaixo para assumir a responsabilidade técnica. Após adotar, você poderá montar treinos e registrar avaliações para ele.</p>

    <table>
        <thead>
            <tr>
                <th>Nome do Aluno</th>
                <th style="text-align: center; width: 180px;">Ação Operacional</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while($aluno = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($aluno['nome']); ?></strong></td>
                        <td style="text-align: center;">
                            <a href="../actions/processa_vinculo_instrutor.php?aluno_id=<?php echo $aluno['id']; ?>&inst_id=<?php echo $id_instrutor_real; ?>" class="btn-adotar">
                                 Adotar Aluno
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="text-align:center; padding: 40px; color: #999; font-style: italic;">
                        Não há novos alunos aguardando vínculo no momento. Todos já possuem instrutores atribuídos!
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>