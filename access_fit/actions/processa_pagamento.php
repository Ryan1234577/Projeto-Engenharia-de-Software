<?php
session_start();
include('../config/config.php');


if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1') {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}


$usuario_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;
$perfil     = isset($_SESSION['perfil']) ? strtolower(trim($_SESSION['perfil'])) : '';

if ($usuario_id === 0) {
    if ($perfil === 'coordenador') {
        header("Location: ../coordenador/registrar_pagamento.php?msg=erro");
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada. Faça login novamente.']);
    }
    exit();
}


if ($perfil === 'coordenador') {
    $aluno_id        = isset($_POST['aluno_id']) ? intval($_POST['aluno_id']) : 0;
    $plano_id        = isset($_POST['plano_id']) ? intval($_POST['plano_id']) : 1; 
    $valor_pago      = isset($_POST['valor']) ? $_POST['valor'] : '0.00';
    $data_pagamento  = isset($_POST['data_pagamento']) ? trim($_POST['data_pagamento']) : date('Y-m-d');
    $forma_pagamento = isset($_POST['forma_pagamento']) ? trim($_POST['forma_pagamento']) : 'pix';
    
    $status_final    = 'em dia'; 
    $mes_referencia  = date('Y-m', strtotime($data_pagamento));

    if (strpos($valor_pago, ',') !== false) {
        $valor_pago = str_replace(['.', ','], ['', '.'], $valor_pago);
    }
    $valor_pago = floatval($valor_pago);

    $id_para_atualizar = 0;
    if ($aluno_id > 0) {
        $stmt_check = $conn->prepare("SELECT id FROM pagamentos WHERE aluno_id = ? AND status = 'atrasado' LIMIT 1");
        $stmt_check->bind_param("i", $aluno_id);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result()->fetch_assoc();
        if ($res_check) {
            $id_para_atualizar = intval($res_check['id']);
        }
        $stmt_check->close();
    }

    if ($id_para_atualizar > 0) {
        $stmt_up_pag = $conn->prepare("UPDATE pagamentos SET plano_id = ?, valor = ?, data_pagamento = ?, forma_pagamento = ?, status = ? WHERE id = ?");
        $stmt_up_pag->bind_param("issssi", $plano_id, $valor_pago, $data_pagamento, $forma_pagamento, $status_final, $id_para_atualizar);
        $sucesso_execucao = $stmt_up_pag->execute();
        $stmt_up_pag->close();
    } else {
        $stmt_ins = $conn->prepare("INSERT INTO pagamentos (aluno_id, plano_id, valor, mes_referencia, data_pagamento, forma_pagamento, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_ins->bind_param("iisssss", $aluno_id, $plano_id, $valor_pago, $mes_referencia, $data_pagamento, $forma_pagamento, $status_final);
        $sucesso_execucao = $stmt_ins->execute();
        $stmt_ins->close();
    }

    if ($sucesso_execucao) {
        if ($aluno_id > 0) {
            $stmt_up_aluno = $conn->prepare("UPDATE alunos SET status = 'ativo' WHERE id = ?");
            $stmt_up_aluno->bind_param("i", $aluno_id);
            $stmt_up_aluno->execute();
            $stmt_up_aluno->close();
        }
        header("Location: ../coordenador/registrar_pagamento.php?msg=sucesso");
    } else {
        header("Location: ../coordenador/registrar_pagamento.php?msg=erro");
    }
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$plano_id = isset($_POST['plano_id']) ? intval($_POST['plano_id']) : 1;
$forma_pagamento = isset($_POST['forma_pagamento']) ? trim($_POST['forma_pagamento']) : 'pix';

try {
    $stmt_aluno = $conn->prepare("SELECT id FROM alunos WHERE usuario_id = ?");
    $stmt_aluno->bind_param("i", $usuario_id);
    $stmt_aluno->execute();
    $res_aluno = $stmt_aluno->get_result()->fetch_assoc();
    $aluno_id = $res_aluno ? intval($res_aluno['id']) : 0;
    $stmt_aluno->close();

    if ($aluno_id === 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Aluno não encontrado.']);
        exit();
    }

    $mes_referencia = date('Y-m');
    $data_hoje = date('Y-m-d');

    $stmt_ja_pago = $conn->prepare("SELECT id FROM pagamentos WHERE aluno_id = ? AND mes_referencia = ? AND status = 'em dia' LIMIT 1");
    $stmt_ja_pago->bind_param("is", $aluno_id, $mes_referencia);
    $stmt_ja_pago->execute();
    $res_ja_pago = $stmt_ja_pago->get_result()->fetch_assoc();
    $stmt_ja_pago->close();

    if ($res_ja_pago) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Você já realizou o pagamento deste mês de referência.']);
        exit();
    }

    
    $valor_plano = 50.00;
    $stmt_plano = $conn->prepare("SELECT valor FROM planos WHERE id = ?");
    if ($stmt_plano) {
        $stmt_plano->bind_param("i", $plano_id);
        $stmt_plano->execute();
        $res_plano = $stmt_plano->get_result()->fetch_assoc();
        if ($res_plano) {
            $valor_plano = floatval($res_plano['valor']);
        }
        $stmt_plano->close();
    }
    
    
    $stmt_ch_al = $conn->prepare("SELECT id FROM pagamentos WHERE aluno_id = ? AND status = 'atrasado' LIMIT 1");
    $stmt_ch_al->bind_param("i", $aluno_id);
    $stmt_ch_al->execute();
    $res_ch_al = $stmt_ch_al->get_result()->fetch_assoc();
    $stmt_ch_al->close();

    if ($res_ch_al) {
        $stmt_final = $conn->prepare("UPDATE pagamentos SET plano_id = ?, valor = ?, data_pagamento = ?, forma_pagamento = ?, status = 'em dia' WHERE id = ?");
        $stmt_final->bind_param("isssi", $plano_id, $valor_plano, $data_hoje, $forma_pagamento, $res_ch_al['id']);
    } else {
        $stmt_final = $conn->prepare("INSERT INTO pagamentos (aluno_id, plano_id, valor, mes_referencia, data_pagamento, forma_pagamento, status) VALUES (?, ?, ?, ?, ?, ?, 'em dia')");
        $stmt_final->bind_param("iissss", $aluno_id, $plano_id, $valor_plano, $mes_referencia, $data_hoje, $forma_pagamento);
    }

    if ($stmt_final->execute()) {
        $stmt_final->close();
        
        $stmt_act = $conn->prepare("UPDATE alunos SET status = 'ativo' WHERE id = ?");
        $stmt_act->bind_param("i", $aluno_id);
        $stmt_act->execute();
        $stmt_act->close();

        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao executar salvamento no banco.']);
    }

} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit();