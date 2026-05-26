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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Plano - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .navbar { background-color: #4e0000; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .navbar h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .container { max-width: 500px; margin: 50px auto; padding: 0 20px; }
        .form-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 5px solid #4e0000; }
        .form-box h2 { color: #4e0000; margin-top: 0; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-size: 18px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: bold; color: #495057; margin-bottom: 5px; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        input[type="text"]:focus, input[type="number"]:focus, select:focus { border-color: #4e0000; outline: none; }
        .btn-submit { background: #4e0000; color: white; border: none; padding: 12px 20px; border-radius: 5px; font-size: 14px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #d10e0e; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 14px; }
        .back-link:hover { color: #4e0000; }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Access Fit <span style="font-weight: 300; font-size: 16px;">| Novo Plano</span></h1>
</div>

<div class="container">
    <div class="form-box">
        <h2>Cadastrar Plano</h2>
        <form action="../actions/processa_plano.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome do Plano</label>
                <input type="text" id="nome" name="nome" placeholder="Ex: Plano Ouro" required>
            </div>
            
            <div class="form-group">
                <label for="valor">Valor Mensal (R$)</label>
                <input type="number" id="valor" name="valor" step="0.01" min="0.00" placeholder="0.00" required>
            </div>
            
            <div class="form-group">
                <label for="periodo">Período / Duração</label>
                <select id="periodo" name="periodo" required>
                    <option value="">-- Selecione o Período --</option>
                    <option value="mensal">Mensal</option>
                    <option value="trimestral">Trimestral</option>
                    <option value="anual">Anual</option>
                </select>
            </div>
            
            <button type="submit" class="btn-submit">CADASTRAR PLANO</button>
            <a href="dashboard_coordenador.php" class="back-link">Voltar ao Painel</a>
        </form>
    </div>
</div>

</body>
</html>