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


$id_usuario_logado = $_SESSION['usuario_id'];
$res_inst = $conn->query("SELECT id FROM instrutores WHERE usuario_id = $id_usuario_logado");
if ($res_inst && $res_inst->num_rows > 0) {
    $instrutor_data = $res_inst->fetch_assoc();
    $id_instrutor_real = $instrutor_data['id'];
} else {
    die("Erro: Instrutor correspondente não encontrado no banco de dados.");
}


if (!isset($_GET['aluno_id'])) {
    header("Location: listar_alunos.php");
    exit();
}

$aluno_id = intval($_GET['aluno_id']);


$sql_aluno = "SELECT a.id, u.nome 
              FROM alunos a 
              INNER JOIN usuarios u ON a.usuario_id = u.id 
              WHERE a.id = $aluno_id";
$res_aluno = $conn->query($sql_aluno);

if ($res_aluno && $res_aluno->num_rows > 0) {
    $aluno = $res_aluno->fetch_assoc();
} else {
    echo "<script>alert('Aluno não encontrado!'); window.location.href='listar_alunos.php';</script>";
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peso = floatval($_POST['peso']);
    $altura = floatval($_POST['altura']);
    
    
    $imc = 0;
    if ($altura > 0) {
        $imc = $peso / ($altura * $altura);
    }
    
    $medida_braco = floatval($_POST['medida_braco']);
    $medida_cintura = floatval($_POST['medida_cintura']);
    $medida_quadril = floatval($_POST['medida_quadril']);
    $medida_perna = floatval($_POST['medida_perna']);
    $data_avaliacao = date('Y-m-d');

    
    $sql_insert = "INSERT INTO avaliacoes_fisicas 
                   (aluno_id, instrutor_id, data_avaliacao, peso, altura, imc, medida_braco, medida_cintura, medida_quadril, medida_perna) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql_insert);
    if ($stmt) {
        $stmt->bind_param(
            "iisddddddd", 
            $aluno_id, 
            $id_instrutor_real, 
            $data_avaliacao, 
            $peso, 
            $altura, 
            $imc, 
            $medida_braco, 
            $medida_cintura, 
            $medida_quadril, 
            $medida_perna
        );
        
        if ($stmt->execute()) {
            // REDIRECIONAMENTO AUTOMÁTICO PARA O DASHBOARD DO INSTRUTOR
            echo "<script>
                    alert('Avaliação Física registrada com sucesso!');
                    window.location.href = 'dashboard_instrutor.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Erro ao salvar avaliação: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Erro ao preparar query: " . addslashes($conn->error) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Avaliação Física - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #4e0000; border-bottom: 2px solid #cc0000; padding-bottom: 10px; margin-top: 0; }
        .form-row { display: flex; gap: 15px; margin-bottom: 15px; }
        .form-group { flex: 1; display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 5px; color: #333; font-size: 14px; }
        input[type="number"] { padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .btn-salvar { background: #4e0000; color: white; border: none; padding: 12px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 15px; }
        .btn-salvar:hover { background: #cc0000; }
        .voltar { display: inline-block; margin-bottom: 15px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard_instrutor.php" class="voltar">← Voltar para o menu</a>
    <h2>Avaliação Física</h2>
    <p>Aluno(a): <strong><?php echo htmlspecialchars($aluno['nome']); ?></strong></p>

    <form method="POST" action="">
        <div class="form-row">
            <div class="form-group">
                <label for="peso">Peso (kg):</label>
                <input type="number" step="0.01" name="peso" id="peso" required placeholder="Ex: 75.50">
            </div>
            <div class="form-group">
                <label for="altura">Altura (m):</label>
                <input type="number" step="0.01" name="altura" id="altura" required placeholder="Ex: 1.74">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="medida_braco">Braço (cm):</label>
                <input type="number" step="0.01" name="medida_braco" id="medida_braco" required placeholder="Ex: 35.00">
            </div>
            <div class="form-group">
                <label for="medida_cintura">Cintura (cm):</label>
                <input type="number" step="0.01" name="medida_cintura" id="medida_cintura" required placeholder="Ex: 80.00">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="medida_quadril">Quadril (cm):</label>
                <input type="number" step="0.01" name="medida_quadril" id="medida_quadril" required placeholder="Ex: 95.00">
            </div>
            <div class="form-group">
                <label for="medida_perna">Perna (cm):</label>
                <input type="number" step="0.01" name="medida_perna" id="medida_perna" required placeholder="Ex: 55.00">
            </div>
        </div>

        <button type="submit" class="btn-salvar">Salvar Avaliação Física</button>
    </form>
</div>

</body>
</html>