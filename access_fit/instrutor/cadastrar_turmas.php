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

$id_usuario_logado = $_SESSION['usuario_id'];


$res_inst = $conn->query("SELECT id FROM instrutores WHERE usuario_id = $id_usuario_logado LIMIT 1");
if ($res_inst && $res_inst->num_rows > 0) {
    $instrutor_row = $res_inst->fetch_assoc();
    $id_instrutor_real = $instrutor_row['id'];
} else {
    $id_instrutor_real = $id_usuario_logado; 
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $modalidade   = isset($_POST['modalidade']) ? trim($_POST['modalidade']) : '';
    $dia_semana   = isset($_POST['dia_semana']) ? trim($_POST['dia_semana']) : '';
    $horario      = isset($_POST['horario']) ? trim($_POST['horario']) : '';
    $limite_vagas = intval($_POST['limite_vagas']);

    if (empty($modalidade) || empty($dia_semana) || empty($horario) || $limite_vagas <= 0) {
        echo "<script>alert('Erro: Todos os campos são obrigatórios e devem ser preenchidos corretamente.');</script>";
    } else {
        $sql = "INSERT INTO turmas (modalidade, dia_semana, horario, limite_vagas, instrutor_id) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssii", $modalidade, $dia_semana, $horario, $limite_vagas, $id_instrutor_real);

            if ($stmt->execute()) {
                echo "<script>
                        alert('Turma de $modalidade cadastrada com sucesso.'); 
                        window.location.href='dashboard_instrutor.php';
                      </script>";
                exit();
            } else {
                echo "<script>alert('Erro ao inserir: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Erro na preparação: " . addslashes($conn->error) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Turmas - Access Fit</title>
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
            max-width: 420px; 
            border-top: 5px solid #cc0000;
        }
        h2 { 
            margin-top: 0; 
            color: #4e0000; 
            font-size: 24px;
            border-bottom: 2px solid #f4f4f4;
            padding-bottom: 10px;
        }
        p {
            font-size: 13px;
            color: #666;
            margin-top: -5px;
            margin-bottom: 20px;
        }
        label { 
            display: block; 
            margin: 12px 0 5px; 
            color: #333; 
            font-size: 14px; 
            font-weight: bold;
        }
        input, select { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            box-sizing: border-box; 
            font-size: 14px;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #cc0000;
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background: #4e0000; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            margin-top: 25px; 
            cursor: pointer; 
            font-weight: bold; 
            font-size: 15px;
            transition: background 0.2s;
        }
        button:hover { 
            background: #2a0000; 
        }
        .voltar { 
            display: block; 
            text-align: center; 
            margin-top: 15px; 
            text-decoration: none; 
            color: #666; 
            font-size: 14px; 
        }
        .voltar:hover {
            color: #cc0000;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Nova Turma</h2>
    <p>Preencha os dados abaixo para disponibilizar uma nova grade de aulas.</p>
    
    <form method="POST">
        <label>Modalidade de Aula:</label>
        <select name="modalidade" required>
            <option value="" disabled selected>Selecione uma opção...</option>
            <option value="Spinning">Spinning</option>
            <option value="Musculação">Musculação</option>
            <option value="Crossfit">Crossfit</option>
            <option value="Pilates">Pilates</option>
            <option value="Treino Funcional">Treino Funcional</option>
            <option value="Aulas Coletivas e Dança">Aulas Coletivas e Dança</option>
            <option value="Pilates e Mobilidade">Pilates e Mobilidade</option>
            <option value="Lutas">Lutas</option>
            <option value="Cardio">Cardio</option>
        </select>

        <label>Dia da Semana:</label>
        <select name="dia_semana" required>
            <option value="" disabled selected>Selecione o dia...</option>
            <option value="Segunda-feira">Segunda-feira</option>
            <option value="Terça-feira">Terça-feira</option>
            <option value="Quarta-feira">Quarta-feira</option>
            <option value="Quinta-feira">Quinta-feira</option>
            <option value="Sexta-feira">Sexta-feira</option>
            <option value="Sábado">Sábado</option>
        </select>

        <label>Horário de Início:</label>
        <input type="time" name="horario" required>

        <label>Limite Máximo de Vagas:</label>
        <input type="number" name="limite_vagas" min="1" placeholder="Ex: 15" required>

        <button type="submit">Salvar e Publicar Turma</button>
    </form>
    
    <a href="dashboard_instrutor.php" class="voltar">← Cancelar e Voltar</a>
</div>

</body>
</html>