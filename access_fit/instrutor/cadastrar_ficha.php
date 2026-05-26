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


$id_recebido_url = isset($_GET['aluno_id']) ? intval($_GET['aluno_id']) : null;
$aluno_selecionado_id = null;


if ($id_recebido_url) {
    $stmt_busca_aluno = $conn->prepare("SELECT id FROM alunos WHERE id = ? LIMIT 1");
    $stmt_busca_aluno->bind_param("i", $id_recebido_url);
    $stmt_busca_aluno->execute();
    $res_busca_aluno = $stmt_busca_aluno->get_result()->fetch_assoc();
    
    if ($res_busca_aluno) {
        $aluno_selecionado_id = intval($res_busca_aluno['id']); 
    }
    $stmt_busca_aluno->close();
}


$id_usuario_logado = intval($_SESSION['usuario_id']);
$stmt_real_inst = $conn->prepare("SELECT id FROM instrutores WHERE usuario_id = ? LIMIT 1");
$stmt_real_inst->bind_param("i", $id_usuario_logado);
$stmt_real_inst->execute();
$res_real_inst = $stmt_real_inst->get_result()->fetch_assoc();

$instrutor_id_real = 0;
if ($res_real_inst) {
    $instrutor_id_real = intval($res_real_inst['id']); 
}
$stmt_real_inst->close();


if ($instrutor_id_real === 0) {
    die("Erro crítico: Perfil de instrutor não localizado na tabela correspondente.");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aluno_id     = intval($_POST['aluno_id']);
    $dia_semana   = $_POST['dia_semana']; 
    $exercicio    = trim($_POST['exercicio']);
    $series       = intval($_POST['series']);
    $repeticoes   = trim($_POST['repeticoes']);
    $observacao   = trim($_POST['observacao']);
    $data_criacao = date('Y-m-d');

    
    $sql = "INSERT INTO fichas_treino (aluno_id, dia_semana, instrutor_id, exercicio, series, repeticoes, observacao, data_criacao) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    
    $stmt->bind_param("isisisss", $aluno_id, $dia_semana, $instrutor_id_real, $exercicio, $series, $repeticoes, $observacao, $data_criacao);

    if ($stmt->execute()) {
        echo "<script>
                alert('Treino de $dia_semana cadastrado com sucesso!'); 
                window.location.href='dashboard_instrutor.php';
              </script>";
        exit();
    } else {
        echo "Erro ao gravar treino: " . $conn->error;
    }
}


$nome_aluno = "Não selecionado";
if ($aluno_selecionado_id) {
    $info_aluno = $conn->query("SELECT nome FROM alunos WHERE id = '$aluno_selecionado_id'");
    if ($info_aluno && $info_aluno->num_rows > 0) {
        $dados_aluno = $info_aluno->fetch_assoc();
        $nome_aluno = $dados_aluno['nome'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Montar Ficha - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; padding: 20px; }
        .container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h2 { color: #4e0000; margin-bottom: 5px; }
        label { display: block; margin-top: 15px; color: #666; font-size: 14px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 10px; margin-top: 5px; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; }
        .aluno-nome { color: #cc0000; font-weight: bold; }
        button { width: 100%; padding: 12px; background: #d10e0e; color: white; border: none; border-radius: 6px; margin-top: 25px; cursor: pointer; font-weight: bold; }
        button:hover { background: #a80b0b; }
    </style>
</head>
<body>

<div class="container">
    <h2>Montar Treino</h2>
    <p>Aluno: <span class="aluno-nome"><?= htmlspecialchars($nome_aluno) ?></span></p>
    
    <form method="POST">
        <input type="hidden" name="aluno_id" value="<?= $aluno_selecionado_id ?>">

        <label>Dia da Semana:</label>
        <select name="dia_semana" required>
            <option value="Segunda">Segunda-feira</option>
            <option value="Terça">Terça-feira</option>
            <option value="Quarta">Quarta-feira</option>
            <option value="Quinta">Quinta-feira</option>
            <option value="Sexta">Sexta-feira</option>
            <option value="Sábado">Sábado</option>
        </select>

        <label>Exercício:</label>
        <input type="text" name="exercicio" placeholder="Ex: Supino Reto" required>

        <div style="display: flex; gap: 10px;">
            <div style="flex: 1;">
                <label>Séries:</label>
                <input type="number" name="series" placeholder="3" required>
            </div>
            <div style="flex: 1;">
                <label>Repetições:</label>
                <input type="text" name="repeticoes" placeholder="12" required>
            </div>
        </div>

        <label>Observações para o Aluno:</label>
        <textarea name="observacao" rows="3" placeholder="Ex: Manter a postura ereta..."></textarea>

        <button type="submit">Salvar Exercício na Ficha</button>
        <a href="dashboard_instrutor.php" style="display:block; text-align:center; margin-top:15px; color:#666; text-decoration:none; font-size:13px;">Cancelar</a>
    </form>
</div>

</body>
</html>