<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (file_exists('../config/config.php')) {
    include('../config/config.php');
} else {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}

$perfil_validado = isset($_SESSION['perfil']) ? strtolower(trim($_SESSION['perfil'])) : '';


if (!isset($_SESSION['usuario_id']) || $perfil_validado !== 'instrutor') {
    session_unset();
    session_destroy();
    header("Location: ../academia_instrutor.php?erro=acesso_negado");
    exit(); 
}


$id_usuario_logado = $_SESSION['usuario_id'];
$nome_instrutor = $_SESSION['usuario_nome'] ?? 'Instrutor';


$res_inst = $conn->query("SELECT id FROM instrutores WHERE usuario_id = $id_usuario_logado LIMIT 1");
if ($res_inst && $res_inst->num_rows > 0) {
    $instrutor_row = $res_inst->fetch_assoc();
    $id_instrutor_real = $instrutor_row['id'];
} else {
    $id_instrutor_real = $id_usuario_logado; 
}


$query_alunos = $conn->query("
    SELECT id, nome, status 
    FROM alunos 
    WHERE instrutor_id = '$id_instrutor_real'
    ORDER BY nome ASC
");


$query_turmas = $conn->query("
    SELECT id, modalidade, horario, dia_semana 
    FROM turmas 
    WHERE instrutor_id = '$id_instrutor_real' 
    ORDER BY dia_semana ASC
");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Instrutor - Access Fit</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        header {
            background-color: #4e0000;
            color: white;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .container {
            padding: 30px 50px;
            flex-grow: 1;
        }
        .welcome-msg {
            color: #333;
            margin-bottom: 30px;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 30px;
        }
        @media (max-width: 950px) { .cards-grid { grid-template-columns: 1fr; } }

        .card {
            background: white;
            border-left: 5px solid #cc0000;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card h3 { margin-top: 0; color: #4e0000; font-size: 20px; border-bottom: 2px solid #f4f4f4; padding-bottom: 10px; }
        .btn-logout {
            background-color: #cc0000;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .table-dashboard { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table-dashboard th { text-align: left; background: #fafafa; padding: 10px; font-size: 13px; color: #666; border-bottom: 2px solid #eee; }
        .table-dashboard td { padding: 10px; font-size: 13px; border-bottom: 1px solid #f1f1f1; vertical-align: middle; }
        
        .btn-uc { display: inline-block; padding: 5px 8px; font-size: 11px; font-weight: bold; color: white; text-decoration: none; border-radius: 4px; margin-left: 2px; transition: 0.2s; }
        .btn-uc-treino { background-color: #2e7d32; } 
        .btn-uc-treino:hover { background-color: #1b5e20; }
        .btn-uc-aval { background-color: #0288d1; }  
        .btn-uc-aval:hover { background-color: #01579b; }
        
        .btn-add-topo { float: right; background-color: #4e0000; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; text-decoration: none; font-weight: bold; }
        .btn-add-topo:hover { background-color: #2a0000; }
    </style>
</head>
<body>

<header>
    <h1>Access Fit | Área do Instrutor</h1>
    <a href="../actions/logout.php" class="btn-logout">Sair do Sistema</a>
</header>

<div class="container">
    <div class="welcome-msg">
        <h2>Olá, Prof. <?php echo htmlspecialchars($nome_instructor = $nome_instrutor); ?>!</h2>
        <p>Selecione uma das opções abaixo para gerenciar a academia.</p>
    </div>

    <div class="cards-grid">
        
        <div class="card">
            <h3>
                Meus Alunos
                <a href="vincular_alunos.php" class="btn-add-topo">+ Vincular Aluno</a>
            </h3>
            <p style="color: #666; margin-top: -5px; font-size: 13px;">Alunos atualmente sob sua responsabilidade técnica.</p>
            
            <table class="table-dashboard">
                <thead>
                    <tr>
                        <th>Nome do Aluno</th>
                        <th style="text-align: right;">Ações Disponíveis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_alunos && $query_alunos->num_rows > 0): ?>
                        <?php while ($aluno = $query_alunos->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($aluno['nome']); ?></strong></td>
                                <td style="text-align: right;">
                                    <a href="cadastrar_ficha.php?aluno_id=<?php echo $aluno['id']; ?>" class="btn-uc btn-uc-treino" title="Montar Ficha de Treino"> Treino</a>
                                    
                                    <a href="cadastrar_avaliacao.php?aluno_id=<?php echo $aluno['id']; ?>" class="btn-uc btn-uc-aval" title="Registrar Avaliação"> Avaliar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2" style="color: #999; font-style: italic; text-align: center; padding: 20px;">Você ainda não possui alunos vinculados. Clique em "+ Vincular Aluno" acima para começar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>
                Minhas Turmas
                <a href="cadastrar_turmas.php" class="btn-add-turma btn-add-topo">+ Nova Turma</a>
            </h3>
            <table class="table-dashboard">
                <thead>
                    <tr>
                        <th>Modalidade</th>
                        <th>Horário / Dia</th>
                        <th style="text-align: right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_turmas && $query_turmas->num_rows > 0): ?>
                        <?php while ($turma = $query_turmas->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($turma['modalidade']); ?></strong></td>
                                <td><?php echo htmlspecialchars($turma['dia_semana']); ?> - <?php echo htmlspecialchars($turma['horario']); ?></td>
                                <td style="text-align: right;">
                                    <a href="gerenciar_turmas.php?id=<?php echo $turma['id']; ?>" class="btn-uc btn-uc-aval" title="Gerenciar Horários"> Horário </a>
                                    
                                    <a href="lista_alunos_turma.php?turma_id=<?php echo $turma['id']; ?>" class="btn-uc btn-uc-treino" title="Ver Lista de Alunos"> Alunos </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Nenhuma turma sob sua gerência ativa.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>