<?php
session_start();

if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aluno_id = isset($_POST['aluno_id']) ? intval($_POST['aluno_id']) : 0;
    $plano_id = isset($_POST['plano_id']) ? intval($_POST['plano_id']) : 0;

    if ($aluno_id > 0 && $plano_id > 0) {
        
        // 1. Busca o valor do plano selecionado
        $stmt_plano = $conn->prepare("SELECT valor FROM planos WHERE id = ? LIMIT 1");
        $stmt_plano->bind_param("i", $plano_id);
        $stmt_plano->execute();
        $res_plano = $stmt_plano->get_result()->fetch_assoc();
        
        if (!$res_plano) {
            echo "<script>alert('Plano não localizado.'); window.history.back();</script>";
            exit();
        }
        
        $valor_plano = $res_plano['valor'];
        $data_atual = date('Y-m-d');
        $mes_referencia = date('Y-m'); // Formato: YYYY-MM obrigatório no seu banco
        $status_atrasado = 'atrasado'; // Status correto para disparar o painel de pendências
        $forma_padrao = 'dinheiro';

        // 2. CORREÇÃO DA QUERY: Agora inclui aluno_id, plano_id, valor, mes_referencia, data_pagamento, forma_pagamento e status
        $stmt_insert = $conn->prepare("INSERT INTO pagamentos (aluno_id, plano_id, valor, mes_referencia, data_pagamento, forma_pagamento, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("iidssss", $aluno_id, $plano_id, $valor_plano, $mes_referencia, $data_atual, $forma_padrao, $status_atrasado);

        if ($stmt_insert->execute()) {
            
            // 3. ATUALIZAÇÃO ADICIONAL: Altera o status do aluno para 'inadimplente' na tabela alunos
            // para garantir que ele apareça nos filtros visuais de pendências do coordenador
            $stmt_update_aluno = $conn->prepare("UPDATE alunos SET status = 'inadimplente' WHERE id = ?");
            $stmt_update_aluno->bind_param("i", $aluno_id);
            $stmt_update_aluno->execute();
            $stmt_update_aluno->close();

            echo "<script>
                    alert('Plano vinculado com sucesso! O aluno agora consta em débito no sistema.');
                    window.location.href='../coordenador/dashboard_coordenador.php';
                  </script>";
        } else {
            echo "<script>alert('Erro ao processar vínculo no banco de dados: " . $conn->error . "'); window.history.back();</script>";
        }
        
        $stmt_plano->close();
        $stmt_insert->close();
    } else {
        echo "<script>alert('Erro nos parâmetros enviados.'); window.history.back();</script>";
    }
} else {
    header("Location: ../coordenador/dashboard_coordenador.php");
    exit();
}
$conn->close();
?>