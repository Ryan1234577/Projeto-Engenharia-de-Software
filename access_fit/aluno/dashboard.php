<?php
session_start();


if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Por favor, contate o administrador.");
}
include('../config/config.php'); 


$perfil_validado = isset($_SESSION['perfil']) ? strtolower(trim($_SESSION['perfil'])) : '';


if (!isset($_SESSION['usuario_id']) || $perfil_validado !== 'aluno') {
    session_unset();
    session_destroy();
    header("Location: ../academia.php?erro=acesso_negado");
    exit(); 
}

$id_logado   = $_SESSION['usuario_id'];
$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Aluno';


$stmt_aluno = $conn->prepare("SELECT id FROM alunos WHERE usuario_id = ?");
$stmt_aluno->bind_param("i", $id_logado);
$stmt_aluno->execute();
$res_aluno = $stmt_aluno->get_result()->fetch_assoc();
$aluno_id_real = $res_aluno ? $res_aluno['id'] : 0;


$sql_imc = "SELECT imc, peso, altura, data_avaliacao FROM avaliacoes_fisicas WHERE aluno_id = ? ORDER BY data_avaliacao DESC LIMIT 1";
$stmt_imc = $conn->prepare($sql_imc);
$stmt_imc->bind_param("i", $aluno_id_real);
$stmt_imc->execute();
$res_imc = $stmt_imc->get_result()->fetch_assoc();

$imc_atual  = isset($res_imc['imc']) ? number_format($res_imc['imc'], 2) : "N/A";
$peso_atual = isset($res_imc['peso']) ? number_format($res_imc['peso'], 2) . "kg" : "--";
$data_av    = isset($res_imc['data_avaliacao']) ? date('d/m/Y', strtotime($res_imc['data_avaliacao'])) : "Sem dados";


$treinos_aluno = [];
if ($aluno_id_real > 0) {
    $stmt_treino = $conn->prepare("SELECT id, dia_semana, exercicio, series, repeticoes, observacao FROM fichas_treino WHERE aluno_id = ? ORDER BY id ASC");
    if ($stmt_treino) {
        $stmt_treino->bind_param("i", $aluno_id_real);
        $stmt_treino->execute();
        $result_treino = $stmt_treino->get_result();
        while ($row = $result_treino->fetch_assoc()) {
            $treinos_aluno[] = $row;
        }
        $stmt_treino->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Aluno - Access Fit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Arial', sans-serif; background: linear-gradient(135deg, #000, #d10e0e); min-height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0; padding: 20px; }
        .phone-mockup { width: 100%; max-width: 380px; background: #fff; border-radius: 40px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5); padding: 30px; box-sizing: border-box; max-height: 95vh; overflow-y: auto; }
        .health-card { background: #f8f9fa; border-radius: 15px; padding: 15px; margin-top: 15px; border-left: 5px solid #d10e0e; display: flex; justify-content: space-around; align-items: center; }
        .health-data { text-align: center; }
        .health-data small { color: #888; display: block; font-size: 11px; }
        .health-data span { font-weight: bold; color: #333; font-size: 16px; }
        h2 { color: #000; text-align: center; font-size: 22px; margin-bottom: 10px; }
        h3 { color: #333; font-size: 18px; margin-top: 25px; text-align: center; }
        select { width: 100%; padding: 12px; margin: 10px 0; border-radius: 10px; border: 1px solid #ddd; background: #f8f9fa; cursor: pointer; }
        
        .table-container { margin-top: 15px; border-radius: 12px; overflow: hidden; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { background-color: #e9ecef; padding: 12px; text-align: left; color: #444; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .treinador-img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; vertical-align: middle; margin-right: 8px; }
        .btn-logout { background: #dc3545; color: white; border: none; padding: 12px; width: 100%; border-radius: 10px; margin-top: 30px; cursor: pointer; font-weight: bold; text-align: center; display: block; text-decoration: none; }
        .btn-pay { width: 100%; background: #28a745; color: white; border: none; padding: 12px; border-radius: 10px; cursor: pointer; margin-top: 15px; font-weight: bold; font-size: 16px; }
        .plan-area { background: #fff5f5; padding: 15px; border-radius: 15px; margin-top: 20px; border: 1px dashed #d10e0e; }

        .btn-checkin { 
            width: 100%; 
            background: #000; 
            color: #fff; 
            border: 2px solid #d10e0e; 
            padding: 12px; 
            border-radius: 10px; 
            cursor: pointer; 
            margin-top: 10px; 
            font-weight: bold; 
            transition: 0.3s;
        }
        .btn-checkin:disabled { background: #28a745; border-color: #28a745; cursor: not-allowed; }
        
        .btn-cancelar-agendamento {
            width: 100%;
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 8px;
            font-weight: bold;
            font-size: 13px;
            transition: 0.3s;
        }
        .btn-cancelar-agendamento:hover { background: #bd2130; }
        
        /* Estilos de Pagamento */
        .payment-methods { display: flex; gap: 10px; margin: 15px 0; }
        .payment-methods input[type="radio"] { display: none; }
        .payment-card { flex: 1; border: 2px solid #eee; border-radius: 12px; padding: 10px; text-align: center; cursor: pointer; transition: 0.3s; background: #fff; }
        .payment-card i { font-size: 24px; display: block; margin-bottom: 5px; color: #555; }
        .payment-card span { font-weight: bold; font-size: 14px; display: block; }
        .payment-card small { color: #28a745; font-size: 10px; font-weight: bold; }
        .payment-methods input[type="radio"]:checked + .payment-card { border-color: #d10e0e; background: #fff5f5; color: #d10e0e; transform: scale(1.05); }

        /* Estilos Ajustados para os Seus Campos da Ficha */
        .workout-area { background: #fdfdfd; padding: 15px; border-radius: 15px; margin-top: 25px; border: 1px solid #ddd; }
        .workout-box { background: #fff; border: 1px solid #eee; border-left: 4px solid #d10e0e; border-radius: 10px; padding: 12px; margin-top: 10px; }
        .workout-day { font-weight: bold; color: #000; font-size: 14px; margin: 0; text-transform: uppercase; }
        .workout-exe { font-weight: bold; color: #d10e0e; font-size: 15px; margin: 5px 0 2px 0; }
        .workout-details { font-size: 13px; color: #333; margin: 0 0 5px 0; }
        .workout-obs { font-size: 12px; color: #666; font-style: italic; margin: 0; background: #f8f9fa; padding: 5px; border-radius: 5px; }
        .alert-no-workout { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 15px; border-radius: 10px; font-size: 13px; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>

<div class="phone-mockup">
    <h2>Bem-vindo(a),<br><?php echo htmlspecialchars($nomeUsuario); ?>!</h2>

    <div class="health-card">
        <div class="health-data"><small>IMC ATUAL</small><span><?php echo $imc_atual; ?></span></div>
        <div class="health-data"><small>PESO</small><span><?php echo $peso_atual; ?></span></div>
        <div class="health-data"><small>ÚLTIMA AV.</small><span><?php echo $data_av; ?></span></div>
    </div>
    
    <div class="workout-area">
        <h3 style="margin-top:0; font-size: 16px;"><i class="bi bi-person-workspace"></i> Ficha de Treino</h3>
        <?php if (empty($treinos_aluno)): ?>
            <div class="alert-no-workout">
                <strong>Nenhum treino disponível.</strong><br>
                Solicite ao seu instrutor para que ele monte e forneça o seu treino!
            </div>
        <?php else: ?>
            <?php foreach ($treinos_aluno as $treino): ?>
                <div class="workout-box">
                    <p class="workout-day"><i class="bi bi-calendar-event"></i> <?php echo htmlspecialchars($treino['dia_semana']); ?></p>
                    <p class="workout-exe"><?php echo htmlspecialchars($treino['exercicio']); ?></p>
                    <p class="workout-details">
                        <strong>Séries:</strong> <?php echo htmlspecialchars($treino['series']); ?> | 
                        <strong>Repetições:</strong> <?php echo htmlspecialchars($treino['repeticoes']); ?>
                    </p>
                    <?php if (!empty($treino['observacao'])): ?>
                        <p class="workout-obs"><strong>Obs:</strong> <?php echo htmlspecialchars($treino['observacao']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h3>Presença na Aula</h3>
    <label style="font-size: 12px; color: #666; font-weight: bold;">Selecione a Modalidade:</label>
    <select id="selectTurmaCheckin" onchange="atualizarInterfaceAcoes()">
        <?php
        $res_t = $conn->query("SELECT id, modalidade, horario FROM turmas ORDER BY modalidade ASC");
        if ($res_t && $res_t->num_rows > 0) {
            while($t = $res_t->fetch_assoc()) {
                echo "<option value='{$t['id']}'>".ucfirst($t['modalidade'])." - ".date('H:i', strtotime($t['horario']))."</option>";
            }
        } else {
            echo "<option value=''>Nenhuma turma disponível</option>";
        }
        ?>
    </select>

    <div id="containerAcoesAula">
        <button onclick="gerenciarAcaoAula()" id="btnCheckin" class="btn-checkin">REALIZAR CHECK-IN HOJE</button>
        <button onclick="cancelarAgendamento()" id="btnCancelarAgendamento" class="btn-cancelar-agendamento" style="display: none;">
            CANCELAR AGENDAMENTO
        </button>
    </div>

    <h3>Escolha seu Treinador</h3>
    <select id="selectTreinador" onchange="atualizarTreinador()">
        <option value="">Selecione um Treinador</option>
        <option value="Renato Cariani|imgens/treiner.avif">Renato Cariani</option>
        <option value="Luis Fernando|imgens/91075fee1e72a27639b0eea74bd056be (1).jpg">Luis Fernando</option>
        <option value="Renata Ferreira|imgens/5b8ef4f40fe44.jpg">Renata Ferreira</option>
        <option value="Livia Andrade|imgens/5f7e4ff86f3d8.jpg">Livia Andrade</option>
    </select>

    <div id="treinadorDestaque" style="text-align: center; display: none;">
        <img id="fotoGrande" src="" style="width: 80px; height: 80px; border-radius: 50%; margin-top: 10px; border: 2px solid #d10e0e;">
        <p id="nomeGrande" style="font-weight: bold; margin-top: 5px;"></p>
    </div>

    <h3>Relatório Semanal</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr><th>Dia</th><th>Treino</th><th>Treinador</th></tr>
            </thead>
            <tbody id="corpoTabela"></tbody>
        </table>
    </div>

    <div class="plan-area">
        <h3>Pagamento de Mensalidade</h3>
        <label style="font-size: 13px; font-weight: bold; color: #555;">Forma de Pagamento:</label>
        <div class="payment-methods">
            <input type="radio" name="forma_pagamento" id="pix" value="pix" checked>
            <label for="pix" class="payment-card">
                <i class="bi bi-qr-code-scan"></i>
                <span>Pix</span>
            </label>

            <input type="radio" name="forma_pagamento" id="cartao" value="cartao">
            <label for="cartao" class="payment-card">
                <i class="bi bi-credit-card"></i>
                <span>Cartão</span>
            </label>
        </div>

        <label style="font-size: 13px; font-weight: bold; color: #555;">Seu Plano:</label>
        <select id="selectPlano">
            <option value="">Selecione um Plano</option>
            <?php
            $res_p = $conn->query("SELECT id, nome, valor FROM planos ORDER BY valor ASC");
            if ($res_p) {
                while($p = $res_p->fetch_assoc()) {
                    echo "<option value='{$p['id']}'>{$p['nome']} - R$ ".number_format($p['valor'], 2, ',', '.')."</option>";
                }
            }
            ?>
        </select>
        <button class="btn-pay" onclick="confirmarPagamentoReal()">Confirmar Mensalidade</button>
    </div>

    <a href="../actions/logout.php" class="btn-logout">Sair da minha área</a>    
</div>

<script>
    const treinos = [
        { dia: "Segunda", treino: "Costas e Bíceps", treinador: "Renato Cariani", foto: "imgens/treiner.avif" },
        { dia: "Terça", treino: "Peito e Tríceps", treinador: "Livia Andrade", foto: "imgens/5f7e4ff86f3d8.jpg" },
        { dia: "Quarta", treino: "Pernas", treinador: "Luis Fernando", foto: "imgens/91075fee1e72a27639b0eea74bd056be (1).jpg" },
        { dia: "Quinta", treino: "Ombros", treinador: "Renata Ferreira", foto: "imgens/5b8ef4f40fe44.jpg" },
        { dia: "Sexta", treino: "Full Body", treinador: "Renato Cariani", foto: "imgens/treiner.avif" }
    ];

    let estadoAtualVaga = "nenhum"; 

    function preencherTabela(filtro = "") {
        const corpo = document.getElementById('corpoTabela');
        corpo.innerHTML = "";
        const lista = filtro ? treinos.filter(t => t.treinador === filtro) : treinos;
        lista.forEach(item => {
            corpo.innerHTML += `<tr><td>${item.dia}</td><td>${item.treino}</td><td><img src="../${item.foto}" class="treinador-img"> ${item.treinador}</td></tr>`;
        });
    }

    function atualizarTreinador() {
        const select = document.getElementById('selectTreinador');
        if(select.value) {
            const [nome, foto] = select.value.split('|');
            document.getElementById('treinadorDestaque').style.display = "block";
            document.getElementById('fotoGrande').src = "../" + foto; 
            document.getElementById('nomeGrande').innerText = nome;
            preencherTabela(nome);
        } else {
            document.getElementById('treinadorDestaque').style.display = "none";
            preencherTabela();
        }
    }

    function atualizarInterfaceAcoes() {
        const turmaId = document.getElementById('selectTurmaCheckin').value;
        const btnMain = document.getElementById('btnCheckin');
        const btnCancel = document.getElementById('btnCancelarAgendamento');

        if (!turmaId) return;

        fetch('../actions/verificar_estado_vaga.php?turma_id=' + turmaId)
        .then(response => response.text())
        .then(text => {
            console.log("Resposta do verificar_estado_vaga:", text);
            try {
                const jsonInicio = text.indexOf('{');
                if(jsonInicio === -1) {
                    fallbackSeguranca();
                    return;
                }
                const data = JSON.parse(text.substring(jsonInicio));
                estadoAtualVaga = data.status;
                
                if (estadoAtualVaga === 'agendado') {
                    btnMain.style.background = "#000";
                    btnMain.style.borderColor = "#d10e0e";
                    btnMain.textContent = 'CONFIRMAR PRESENÇA (CHECK-IN)';
                    btnMain.disabled = false;
                    btnCancel.style.display = "block";
                } else if (estadoAtualVaga === 'presenca') {
                    btnMain.style.background = "#28a745";
                    btnMain.style.borderColor = "#28a745";
                    btnMain.textContent = 'CHECK-IN REALIZADO';
                    btnMain.disabled = true;
                    btnCancel.style.display = "none";
                } else {
                    btnMain.style.background = "#0056b3";
                    btnMain.style.borderColor = "#004085";
                    btnMain.textContent = 'AGENDAR AULA';
                    btnMain.disabled = false;
                    btnCancel.style.display = "none";
                }
            } catch (err) {
                fallbackSeguranca();
            }
        });

        function fallbackSeguranca() {
            btnMain.style.background = "#0056b3";
            btnMain.style.borderColor = "#004085";
            btnMain.textContent = 'AGENDAR AULA';
            btnMain.disabled = false;
            btnCancel.style.display = "none";
        }
    }

    function gerenciarAcaoAula() {
        const turmaId = document.getElementById('selectTurmaCheckin').value;
        if (!turmaId) return;

        if (estadoAtualVaga === 'agendado') {
            fetch('../actions/registrar_presenca.php?turma_id=' + turmaId)
            .then(response => response.text())
            .then(text => {
                try {
                    const jsonInicio = text.indexOf('{');
                    const data = JSON.parse(text.substring(jsonInicio));
                    if (data.sucesso) {
                        alert("Presença registrada com sucesso! Bom treino.");
                        atualizarInterfaceAcoes();
                    } else {
                        alert("Erro do Sistema: " + data.mensagem);
                    }
                } catch(e) {
                    alert("Erro crítico na presença. Verifique o console F12.");
                    console.error(text);
                }
            });
        } else {
            const dados = new FormData();
            dados.append('turma_id', turmaId);
            dados.append('acao', 'agendar');

            fetch('../actions/processa_agendamento.php', { method: 'POST', body: dados })
            .then(response => response.text())
            .then(text => {
                console.log("Resposta do processa_agendamento:", text);
                try {
                    const jsonInicio = text.indexOf('{');
                    if (jsonInicio === -1) {
                        alert("O servidor não retornou um formato válido. Resposta: " + text);
                        return;
                    }
                    const data = JSON.parse(text.substring(jsonInicio));
                    if (data.sucesso) {
                        alert("Aula agendada com sucesso!");
                        atualizarInterfaceAcoes();
                    } else {
                        alert("Aviso: " + data.mensagem);
                    }
                } catch (err) {
                    alert("Erro ao processar o agendamento. Resposta recebida: " + text);
                }
            })
            .catch(err => alert("Erro na requisição AJAX: " + err));
        }
    }

    function cancelarAgendamento() {
        const turmaId = document.getElementById('selectTurmaCheckin').value;
        if (!turmaId) return;

        if (confirm("Deseja realmente cancelar este agendamento?")) {
            const dados = new FormData();
            dados.append('turma_id', turmaId);
            dados.append('acao', 'cancelar');

            fetch('../actions/processa_agendamento.php', { method: 'POST', body: dados })
            .then(response => response.text())
            .then(text => {
                const jsonInicio = text.indexOf('{');
                const data = JSON.parse(text.substring(jsonInicio));
                if (data.sucesso) {
                    alert("Agendamento cancelado!");
                    atualizarInterfaceAcoes();
                } else {
                    alert(data.mensagem);
                }
            });
        }
    }

   function confirmarPagamentoReal() {
    const planoSelect = document.getElementById('selectPlano');
    const planoId = planoSelect.value;
    
    const radioForma = document.querySelector('input[name="forma_pagamento"]:checked');
    
    if (!radioForma) { alert("Por favor, selecione uma forma de pagamento."); return; }
    if (!planoId) { alert("Por favor, selecione um plano."); return; }

    const formaPagamento = radioForma.value;

    const dados = new FormData();
    dados.append('plano_id', planoId);
    dados.append('forma_pagamento', formaPagamento);

    fetch('../actions/processa_pagamento.php', { method: 'POST', body: dados })
    .then(response => response.text()) 
    .then(text => {
        console.log("Retorno do servidor:", text);
        try {
            const jsonInicio = text.indexOf('{');
            if (jsonInicio === -1) {
                alert("Resposta inválida do servidor: " + text);
                return;
            }
            const data = JSON.parse(text.substring(jsonInicio));
            if (data.sucesso) { 
                alert("Pagamento Realizado com Sucesso!"); 
                location.reload(); 
            } else { 
                alert("Aviso do Sistema: " + data.mensagem); 
            }
        } catch (err) {
            alert("Erro ao processar o retorno do pagamento. Verifique o console F12.");
        }
    })
    .catch(error => alert("Erro na requisição técnica: " + error));
}

    window.onload = () => {
        preencherTabela();
        atualizarInterfaceAcoes();
    };
</script>
</body>
</html>