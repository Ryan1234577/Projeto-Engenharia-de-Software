<?php
session_start();


if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');


$perfil_validado = isset($_SESSION['perfil']) ? strtolower(trim($_SESSION['perfil'])) : '';
if (!isset($_SESSION['usuario_id']) || $perfil_validado !== 'instrutor') {
    header("Location: ../academia_instrutor.php");
    exit();
}


$turma_id = isset($_GET['turma_id']) ? intval($_GET['turma_id']) : 0;

if ($turma_id <= 0) {
    echo "<script>alert('Turma inválida ou não selecionada.'); window.location.href='dashboard_instrutor.php';</script>";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_aluno'])) {
    $aluno_id = intval($_POST['aluno_id']);
    
    if ($aluno_id > 0) {
        // Verifica se o aluno já não está vinculado nesta turma para evitar duplicados
        $check = $conn->query("SELECT id FROM presencas WHERE aluno_id = $aluno_id AND turma_id = $turma_id");
        
        if ($check && $check->num_rows == 0) {
            // Insere o vínculo de forma simples apenas com as colunas estruturais garantidas
            $stmt_add = $conn->prepare("INSERT INTO presencas (aluno_id, turma_id) VALUES (?, ?)");
            $stmt_add->bind_param("ii", $aluno_id, $turma_id);
            
            if ($stmt_add->execute()) {
                // Mensagem de sucesso e atualização imediata da página e da lista
                echo "<script>alert('Aluno vinculado à turma com sucesso!'); window.location.href='lista_alunos_turma.php?turma_id=$turma_id';</script>";
                exit();
            } else {
                echo "<script>alert('Erro técnico ao vincular aluno.');</script>";
            }
            $stmt_add->close();
        } else {
            echo "<script>alert('Este aluno já faz parte desta turma!');</script>";
        }
    }
}


$sql_turma = "SELECT modalidade, dia_semana, horario FROM turmas WHERE id = $turma_id LIMIT 1";
$res_turma = $conn->query($sql_turma);
$dados_turma = $res_turma->fetch_assoc();


$sql_JA_VINCULADOS = "SELECT a.id, a.nome FROM alunos a 
                      INNER JOIN presencas p ON a.id = p.aluno_id 
                      WHERE p.turma_id = $turma_id 
                      GROUP BY a.id ORDER BY a.nome ASC";
$resultado_vinculados = $conn->query($sql_JA_VINCULADOS);


$sql_TODOS = "SELECT id, nome FROM alunos ORDER BY nome ASC";
$resultado_todos = $conn->query($sql_TODOS);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos da Turma - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4; padding: 30px; margin: 0; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 700px; margin: auto; border-top: 5px solid #cc0000; }
        h2 { margin-top: 0; color: #4e0000; font-size: 22px; }
        h3 { color: #4e0000; font-size: 16px; margin-top: 25px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-turma { font-size: 14px; color: #666; background: #f9f9f9; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 3px solid #cc0000; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #4e0000; color: white; text-align: left; padding: 10px; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; color: #333; }
        .form-add { display: flex; gap: 10px; margin-top: 15px; background: #fdfdfd; padding: 15px; border: 1px dashed #ddd; border-radius: 6px; }
        select { flex-grow: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; background: #fff; }
        .btn-add { background: #28a745; color: white; border: none; padding: 0 20px; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 13px; transition: 0.2s; }
        .btn-add:hover { background: #218838; }
        .voltar { display: block; text-align: center; margin-top: 25px; text-decoration: none; color: #666; font-size: 13px; }
        .voltar:hover { color: #cc0000; }
    </style>
</head>
<body>

<div class="container">
    <h2>Alunos da Turma</h2>
    
    <?php if ($dados_turma): ?>
        <div class="info-turma">
            Modalidade: <strong><?= htmlspecialchars(ucfirst($dados_turma['modalidade'])) ?></strong> | 
            Horário: <strong><?= htmlspecialchars($dados_turma['dia_semana']) ?> às <?= htmlspecialchars($dados_turma['horario']) ?></strong>
        </div>
    <?php endif; ?>

    <h3> Alunos Matriculados Nesta Turma</h3>
    <table>
        <thead>
            <tr>
                <th>ID do Aluno</th>
                <th>Nome do Aluno</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado_vinculados && $resultado_vinculados->num_rows > 0): ?>
                <?php while ($aluno_vinc = $resultado_vinculados->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $aluno_vinc['id'] ?></td>
                        <td><strong><?= htmlspecialchars($aluno_vinc['nome']) ?></strong></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="color: #999; text-align: center; font-style: italic; padding: 15px;">
                        Nenhum aluno incluído nesta turma ainda.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h3> Adicionar Aluno à Turma</h3>
    <form method="POST" action="lista_alunos_turma.php?turma_id=<?= $turma_id ?>" class="form-add">
        <select name="aluno_id" required>
            <option value="" disabled selected>Selecione um aluno do sistema...</option>
            <?php if ($resultado_todos && $resultado_todos->num_rows > 0): ?>
                <?php while ($aluno_todos = $resultado_todos->fetch_assoc()): ?>
                    <option value="<?= $aluno_todos['id'] ?>"><?= htmlspecialchars($aluno_todos['nome']) ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
        <button type="submit" name="adicionar_aluno" class="btn-add">Vincular</button>
    </form>

    <a href="dashboard_instrutor.php" class="voltar">← Voltar para o Dashboard</a>
</div>

</body>
</html>