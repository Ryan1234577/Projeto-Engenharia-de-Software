<?php
session_start();


if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');


$perfil_validado = isset($_SESSION['perfil']) ? strtolower(trim($_SESSION['perfil'])) : '';
if (!isset($_SESSION['usuario_id']) || $perfil_validado !== 'instructor' && $perfil_validado !== 'instrutor') {
    header("Location: ../academia_instrutor.php");
    exit();
}


$turma_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($turma_id <= 0) {
    echo "<script>alert('Turma inválida ou não selecionada.'); window.location.href='dashboard_instrutor.php';</script>";
    exit();
}


$usuario_id = $_SESSION['usuario_id']; 

$sql_instrutor = "SELECT id FROM instrutores WHERE usuario_id = $usuario_id LIMIT 1";
$res_inst = $conn->query($sql_instrutor);
$instrutor_data = $res_inst->fetch_assoc();
$id_instrutor = $instrutor_data['id'];


$sql_turma_especifica = "SELECT * FROM turmas WHERE id = $turma_id AND instrutor_id = $id_instrutor LIMIT 1";
$resultado = $conn->query($sql_turma_especifica);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Horário da Turma - Access Fit</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f4f4f4; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
        }
        .container { 
            background: #fff; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 500px; 
            border-top: 5px solid #cc0000;
        }
        h2 { 
            margin-top: 0; 
            color: #4e0000; 
            font-size: 22px;
            border-bottom: 2px solid #f4f4f4;
            padding-bottom: 10px;
        }
        p.sub {
            font-size: 13px;
            color: #666;
            margin-top: -5px;
            margin-bottom: 20px;
        }
        .card-turma { 
            border-left: 5px solid #4e0000; 
            padding: 20px; 
            background: #fafafa; 
            border-radius: 4px; 
            margin-bottom: 20px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.02);
        }
        .card-turma h3 {
            margin: 0 0 10px 0;
            color: #cc0000;
            font-size: 18px;
        }
        .card-turma p {
            margin: 5px 0;
            font-size: 14px;
            color: #333;
        }
        .vagas { 
            font-size: 12px; 
            color: #777; 
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        .voltar { 
            display: block; 
            text-align: center; 
            margin-top: 20px; 
            text-decoration: none; 
            color: #666; 
            font-size: 14px; 
            font-weight: 500;
        }
        .voltar:hover {
            color: #cc0000;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Informações do Horário</h2>
    <p class="sub">Detalhes operacionais específicos da modalidade selecionada no painel.</p>

    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <?php $turma = $resultado->fetch_assoc(); ?>
        <div class="card-turma">
            <h3><?= htmlspecialchars(ucfirst($turma['modalidade'])) ?></h3>
            <p><strong>Dia da Semana:</strong> <?= htmlspecialchars($turma['dia_semana']) ?></p>
            <p><strong>Horário de Início:</strong> <?= htmlspecialchars($turma['horario']) ?></p>
            <span class="vagas">Limite Máximo: <?= intval($turma['limite_vagas']) ?> alunos permitidos</span>
        </div>
    <?php else: ?>
        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; font-size: 14px; text-align: center;">
             Erro: Turma não localizada ou você não possui permissão técnica sobre ela.
        </div>
    <?php endif; ?>

    <a href="dashboard_instrutor.php" class="voltar">← Voltar para o Dashboard</a>
</div>

</body>
</html>