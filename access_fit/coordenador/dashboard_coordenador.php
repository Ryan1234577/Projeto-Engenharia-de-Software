<?php
session_start();

if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');

// Trava de segurança para garantir que apenas o coordenador logado acesse
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}

// Função auxiliar para verificar se uma coluna existe nas outras tabelas de forma dinâmica
function verificarColuna($conn, $tabela, $coluna) {
    $result = $conn->query("SHOW COLUMNS FROM `$tabela` LIKE '$coluna'");
    return ($result && $result->num_rows > 0);
}

// --- 1. BUSCA DE CONTADORES DOS CARDS ---
$total_usuarios = 0;
$res = $conn->query("SELECT COUNT(*) as t FROM usuarios");
if ($res) $total_usuarios = $res->fetch_assoc()['t'];

$total_alunos = 0;
$res = $conn->query("SELECT COUNT(*) as t FROM alunos WHERE status = 'ativo'");
if ($res) $total_alunos = $res->fetch_assoc()['t'];

$total_instrutores = 0;
$res = $conn->query("SELECT COUNT(*) as t FROM instrutores");
if ($res) $total_instrutores = $res->fetch_assoc()['t'];

$total_turmas = 0;
$res = $conn->query("SELECT COUNT(*) as t FROM turmas");
if ($res) $total_turmas = $res->fetch_assoc()['t'];


// --- 2. CONSULTAS ADAPTATIVAS ---

// Usuários
$query_usuarios = $conn->query("SELECT id, nome, email, perfil FROM usuarios ORDER BY id DESC");

// Alunos
$query_alunos = $conn->query("SELECT a.id, a.usuario_id, a.status, u.nome, u.email FROM alunos a INNER JOIN usuarios u ON a.usuario_id = u.id ORDER BY u.nome ASC");

// Instrutores
$col_email_inst = verificarColuna($conn, 'instrutores', 'email') ? ', email' : '';
$query_instrutores = $conn->query("SELECT id, nome, especialidade $col_email_inst FROM instrutores ORDER BY nome ASC");

// Planos
$col_nome_plano = verificarColuna($conn, 'planos', 'nome_plano') ? 'nome_plano' : 'nome';
$col_duracao = verificarColuna($conn, 'planos', 'duracao') ? ', duracao' : '';
$query_planos = $conn->query("SELECT id, $col_nome_plano as nome, valor $col_duracao FROM planos ORDER BY $col_nome_plano ASC");

// Turmas
$query_turmas = $conn->query("SELECT t.id, t.modalidade, t.dia_semana, t.horario, i.nome as instrutor_nome 
                              FROM turmas t 
                              LEFT JOIN instrutores i ON t.instrutor_id = i.id 
                              ORDER BY t.modalidade ASC");

// Pagamentos
$query_pagamentos = $conn->query("SELECT p.id, u.nome as aluno_nome, p.valor, p.data_pagamento, p.status 
                                  FROM pagamentos p 
                                  INNER JOIN alunos a ON p.aluno_id = a.id 
                                  INNER JOIN usuarios u ON a.usuario_id = u.id 
                                  ORDER BY p.data_pagamento DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Coordenador - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .navbar { background-color: #4e0000; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .navbar h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .logout { color: #ffb3b3; text-decoration: none; font-weight: bold; border: 1px solid #ffb3b3; padding: 8px 15px; border-radius: 5px; transition: 0.3s; }
        .logout:hover { background: #ffb3b3; color: #4e0000; }
        
        .main-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        
        /* Container dos Botões de Ações Rápidas */
        .action-buttons-container { width: 100%; display: flex; justify-content: flex-start; gap: 12px; flex-wrap: wrap; margin-bottom: 25px; }
        .btn-action-panel { display: inline-block; padding: 12px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: transform 0.2s, background-color 0.3s; color: white; }
        .btn-action-panel:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        
        /* Cores customizadas dos botões */
        .btn-orange { background-color: #e65100; } .btn-orange:hover { background-color: #bf4300; }
        .btn-red { background-color: #c62828; } .btn-red:hover { background-color: #a01c1c; }
        .btn-green { background-color: #2e7d32; } .btn-green:hover { background-color: #1b5e20; }
        .btn-burgundy { background-color: #4e0000; border: 1px solid #ffb3b3; } .btn-burgundy:hover { background-color: #350000; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-left: 5px solid #4e0000; }
        .stat-card h3 { margin: 0 0 10px 0; color: #666; font-size: 14px; text-transform: uppercase; }
        .stat-card p { margin: 0; font-size: 28px; font-weight: bold; color: #333; }
        
        .section-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .section-box h2 { color: #4e0000; margin-top: 0; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-size: 18px; }
        
        .table-responsive { width: 100%; overflow-x: auto; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #f8f9fa; padding: 12px; font-size: 14px; font-weight: bold; color: #495057; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; font-size: 14px; border-bottom: 1px solid #eee; color: #495057; }
        
        .btn-edit { background: #4e0000; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; transition: 0.3s; margin-right: 5px;}
        .btn-edit:hover { background: #d10e0e; }
        
        .btn-inactive { background: #6c757d; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; transition: 0.3s; }
        .btn-inactive:hover { background: #5a6268; }
        .badge-inativo { background: #e2e3e5; color: #383d41; }

        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-aluno { background: #e3f2fd; color: #0d47a1; }
        .badge-instrutor { background: #efebe9; color: #4e342e; }
        .badge-coord { background: #fbe9e7; color: #d84315; }
        .badge-pago { background: #e8f5e9; color: #2e7d32; }
        .badge-pendente { background: #fff8e1; color: #f57f17; }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Access Fit <span style="font-weight: 300; font-size: 16px;">| Painel do Coordenador</span></h1>
    <a href="../actions/logout.php" class="logout">Sair do Painel</a>
</div>

<div class="main-container">

    <div class="action-buttons-container">
        <a href="cadastrar_funcionario.php" class="btn-action-panel btn-orange">
            + Cadastrar Funcionário
        </a>
        <a href="gerar_relatorios.php" class="btn-action-panel btn-burgundy">
            Gerar Relatórios
        </a>
        <a href="alunos_inadimplentes.php" class="btn-action-panel btn-red">
            Alunos Inadimplentes
        </a>    
        <a href="registrar_pagamento.php" class="btn-action-panel btn-green">
            $ Registrar Pagamento
        </a>
        <a href="cadastrar_plano.php" class="btn-action-panel btn-burgundy">
            + Cadastrar Plano
        </a>
        <a href="cadastrar_aluno.php" class="btn-action-panel btn-red">
            + Cadastrar Aluno
        </a>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Usuários no Sistema</h3>
            <p><?php echo $total_usuarios; ?></p>
        </div>
        <div class="stat-card">
            <h3>Alunos Ativos</h3>
            <p><?php echo $total_alunos; ?></p>
        </div>
        <div class="stat-card">
            <h3>Instrutores</h3>
            <p><?php echo $total_instrutores; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total de Turmas</h3>
            <p><?php echo $total_turmas; ?></p>
        </div>
    </div>

    <div class="section-box">
        <h2>1. Controle de Usuários do Sistema</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_usuarios && $query_usuarios->num_rows > 0): ?>
                        <?php while ($user = $query_usuarios->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($user['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo ($user['perfil'] === 'aluno') ? 'badge-aluno' : (($user['perfil'] === 'instrutor') ? 'badge-instrutor' : 'badge-coord'); 
                                     ?>">
                                        <?php echo $user['perfil']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="editar_usuario.php?id=<?php echo $user['id']; ?>" class="btn-edit">Editar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Nenhum usuário cadastrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-box">
        <h2>2. Alunos Cadastrados</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Aluno</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_alunos && $query_alunos->num_rows > 0): ?>
                        <?php while ($aluno = $query_alunos->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $aluno['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($aluno['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($aluno['status'] === 'ativo') ? 'badge-pago' : 'badge-inativo'; ?>">
                                        <?php echo htmlspecialchars($aluno['status'] ?? 'ativo'); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="vincular_plano.php?id=<?php echo $aluno['id']; ?>" class="btn-edit" style="background-color: #2e7d32;">Vincular Plano</a>
                                    <a href="editar_usuario.php?id=<?php echo $aluno['usuario_id']; ?>" class="btn-edit">Editar Aluno</a>
                                    <?php if(($aluno['status'] ?? 'ativo') === 'ativo'): ?>
                                        <a href="inativar_aluno.php?id=<?php echo $aluno['usuario_id']; ?>" class="btn-inactive" onclick="return confirm('Tem certeza que deseja INATIVAR este aluno?');">Inativar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Nenhum aluno cadastrado no momento.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-box">
        <h2>3. Equipe de Instrutores</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Especialidade</th>
                        <?php if (verificarColuna($conn, 'instrutores', 'email')): ?><th>E-mail</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_instrutores && $query_instrutores->num_rows > 0): ?>
                        <?php while ($inst = $query_instrutores->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $inst['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($inst['nome']); ?></strong></td>
                                <td><?php echo htmlspecialchars($inst['especialidade'] ?? 'Instrutor Geral'); ?></td>
                                <?php if (isset($inst['email'])): ?><td><?php echo htmlspecialchars($inst['email']); ?></td><?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Nenhum instructor cadastrado no momento.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-box">
        <h2>4. Planos Oferecidos</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Plano</th>
                        <th>Valor Mensal</th>
                        <?php if (verificarColuna($conn, 'planos', 'duracao')): ?><th>Duração</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_planos && $query_planos->num_rows > 0): ?>
                        <?php while ($plano = $query_planos->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $plano['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($plano['nome']); ?></strong></td>
                                <td>R$ <?php echo number_format($plano['valor'], 2, ',', '.'); ?></td>
                                <?php if (isset($plano['duracao'])): ?><td><?php echo $plano['duracao']; ?> meses</td><?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Nenhum plano cadastrado no banco de dados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-box">
        <h2>5. Turmas e Grade Horária</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Turma</th>
                        <th>Modalidade / Atividade</th>
                        <th>Dia da Semana</th>
                        <th>Horário</th>
                        <th>Instrutor Responsável</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_turmas && $query_turmas->num_rows > 0): ?>
                        <?php while ($turma = $query_turmas->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $turma['id']; ?></td>
                                <td><strong style="text-transform: capitalize;"><?php echo htmlspecialchars($turma['modalidade']); ?></strong></td>
                                <td><?php echo htmlspecialchars($turma['dia_semana'] ?? 'Não definido'); ?></td>
                                <td><?php echo $turma['horario']; ?></td>
                                <td><?php echo htmlspecialchars($turma['instrutor_nome'] ?? 'Sem instrutor designado'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Nenhuma turma cadastrada no momento.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="section-box">
        <h2>6. Auditoria de Pagamentos Realizados</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Transação</th>
                        <th>Aluno</th>
                        <th>Valor Pago</th>
                        <th>Data do Pagamento</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($query_pagamentos && $query_pagamentos->num_rows > 0): ?>
                        <?php while ($pag = $query_pagamentos->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $pag['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($pag['aluno_nome']); ?></strong></td>
                                <td>R$ <?php echo number_format($pag['valor'], 2, ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($pag['data_pagamento'])); ?></td>
                                <td>
                                    <span class="badge <?php echo ($pag['status'] === 'pago' || $pag['status'] === 'aprovado' || $pag['status'] === 'em dia') ? 'badge-pago' : 'badge-pendente'; ?>">
                                        <?php echo $pag['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">Nenhum registro de pagamento encontrado no momento.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>