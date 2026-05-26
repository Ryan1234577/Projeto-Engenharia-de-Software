<?php
session_start();
include('config.php');

// Proteção: Só acessa se estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: academia.php");
    exit();
}

// Busca as turmas disponíveis usando os nomes de colunas que vimos no seu print
$sql_turmas = "SELECT id, modalidade, dia_semana, horario FROM turmas";
$res_turmas = $conn->query($sql_turmas);

// Verifica se a consulta falhou para te avisar na tela
if (!$res_turmas) {
    die("Erro na consulta ao banco: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Agendamento - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #111; color: #fff; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background: #fff; color: #333; padding: 30px; border-radius: 15px; width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h2 { text-align: center; color: #d10e0e; margin-top: 0; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        select, input, button { width: 100%; padding: 12px; margin-top: 8px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box; }
        button { background: #d10e0e; color: #fff; font-weight: bold; cursor: pointer; border: none; margin-top: 20px; transition: 0.3s; }
        button:hover { background: #a80b0b; }
        .footer-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Agendar Aula</h2>
        
        <?php if ($res_turmas->num_rows > 0): ?>
            <form action="processa_agendamento.php" method="POST">
                <label>Escolha a Turma/Modalidade:</label>
                <select name="turma_id" required>
                    <option value="">Selecione uma aula...</option>
                    <?php while($turma = $res_turmas->fetch_assoc()): ?>
                        <option value="<?= $turma['id'] ?>">
                            <?= ucfirst($turma['modalidade']) ?> - <?= $turma['dia_semana'] ?> às <?= $turma['horario'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Data que deseja comparecer:</label>
                <input type="date" name="data_aula" required>

                <button type="submit">Confirmar Agendamento</button>
            </form>
        <?php else: ?>
            <p style="color: #666; text-align: center;">Não há turmas disponíveis no momento. Cadastre uma primeiro!</p>
            <a href="cadastrar_turmas.php" style="color: #d10e0e; display: block; text-align: center;">Cadastrar Turma</a>
        <?php endif; ?>

        <a href="dashboard.php" class="footer-link">← Voltar para o Painel</a>
    </div>
</body>
</html>