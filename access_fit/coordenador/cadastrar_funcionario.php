<?php
session_start();

if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');


if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Funcionário - Access Fit</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .navbar { background-color: #4e0000; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .navbar h1 { margin: 0; font-size: 24px; letter-spacing: 1px; }
        .back-link { color: #ffb3b3; text-decoration: none; font-weight: bold; border: 1px solid #ffb3b3; padding: 8px 15px; border-radius: 5px; transition: 0.3s; }
        .back-link:hover { background: #ffb3b3; color: #4e0000; }
        
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .form-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 5px solid #4e0000; }
        .form-box h2 { color: #4e0000; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; font-size: 20px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #495057; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
        .form-control:focus { border-color: #4e0000; outline: none; }
        
        .btn-submit { background: #4e0000; color: white; padding: 12px 20px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: #d10e0e; }
        
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; font-weight: bold; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
    <script>
        
        function alternarCamposPerfil() {
            var perfil = document.getElementById("perfil").value;
            var blocoInstrutor = document.getElementById("bloco-instrutor-exclusivo");
            
            if (perfil === "instrutor") {
                blocoInstrutor.style.display = "block";
                document.getElementById("especialidade").required = true;
                document.getElementById("cref").required = true;
                document.getElementById("telefone").required = true;
            } else {
                blocoInstrutor.style.display = "none";
                document.getElementById("especialidade").required = false;
                document.getElementById("cref").required = false;
                document.getElementById("telefone").required = false;
            }
        }
    </script>
</head>
<body>

<div class="navbar">
    <h1>Access Fit <span style="font-weight: 300; font-size: 16px;">| Novo Funcionário</span></h1>
    <a href="dashboard_coordenador.php" class="back-link">Voltar ao Painel</a>
</div>

<div class="container">
    <div class="form-box">
        <h2>Cadastro de Colaboradores</h2>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'erro_email'): ?>
            <div class="alert alert-error">Este e-mail já está cadastrado no sistema!</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'erro_geral'): ?>
            <div class="alert alert-error">Erro ao efetuar o cadastro. Tente novamente.</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'sucesso'): ?>
            <div class="alert alert-success">Funcionário cadastrado com sucesso!</div>
        <?php endif; ?>

        <form action="../actions/processa_funcionario.php" method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Jean Silva" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail Institucional:</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="exemplo@accessfit.com" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha de Acesso provisória:</label>
                <input type="password" name="senha" id="senha" class="form-control" placeholder="Mínimo 6 caracteres" required>
            </div>

            <div class="form-group">
                <label for="perfil">Perfil do Funcionário:</label>
                <select name="perfil" id="perfil" class="form-control" onchange="alternarCamposPerfil()" required>
                    <option value="">-- Selecione o Cargo --</option>
                    <option value="instrutor">Instrutor / Professor</option>
                </select>
            </div>

            <div id="bloco-instrutor-exclusivo" style="display: none; background: #fffcfc; border: 1px dashed #4e0000; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h4 style="color: #4e0000; margin-top: 0; margin-bottom: 15px;">Dados Profissionais do Instrutor</h4>
                
                <div class="form-group">
                    <label for="especialidade">Especialidade:</label>
                    <input type="text" name="especialidade" id="especialidade" class="form-control" placeholder="Ex: Musculação, Pilates, Crossfit">
                </div>

                <div class="form-group">
                    <label for="cref">CREF (Registro Profissional):</label>
                    <input type="text" name="cref" id="cref" class="form-control" placeholder="Ex: 000000-G/SP">
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone de Contato:</label>
                    <input type="text" name="telefone" id="telefone" class="form-control" placeholder="Ex: (11) 98888-7777">
                </div>

                <div class="form-group">
                    <label for="foto_url">Link da Foto de Perfil (Opcional):</label>
                    <input type="url" name="foto_url" id="foto_url" class="form-control" placeholder="Ex: https://site.com/foto.jpg">
                </div>
            </div>

            <button type="submit" class="btn-submit">Salvar Novo Colaborador</button>
        </form>
    </div>
</div>

</body>
</html>