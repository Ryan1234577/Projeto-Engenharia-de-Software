<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Coordenador - Access Fit</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #111;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }
        .cadastro-container {
            background: #1a1a1a;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(255, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            border-top: 4px solid #ff0000;
        }
        h2 {
            text-align: center;
            color: #ff0000;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        p {
            text-align: center;
            color: #ccc;
            font-size: 14px;
            margin-bottom: 25px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #eee;
            font-size: 14px;
        }
        input {
            padding: 12px;
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 5px;
            font-size: 14px;
            color: white;
            transition: 0.3s;
        }
        input:focus {
            border-color: #ff0000;
            outline: none;
        }
        .btn-cadastrar {
            background: #ff0000;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            transition: 0.3s;
            text-transform: uppercase;
        }
        .btn-cadastrar:hover {
            background: #b30000;
        }
        .link-login {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #ccc;
        }
        .link-login a {
            color: #ff0000;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .link-login a:hover {
            color: #ff6666;
        }
    </style>
</head>
<body>

<div class="cadastro-container">
    <h2>Access Fit</h2>
    <p>Área Administrativa - Registrar Coordenador</p>

    <form action="../actions/processa_cadastro_coordenador.php" method="POST">
        <div class="form-group">
            <label for="nome">Nome Completo:</label>
            <input type="text" id="nome" name="nome" required placeholder="Digite seu nome">
        </div>

        <div class="form-group">
            <label for="email">E-mail de Acesso:</label>
            <input type="email" id="email" name="email" required placeholder="coordenador@accessfit.com">
        </div>

        <div class="form-group">
            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" required placeholder="(11) 99999-9999">
        </div>

        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required placeholder="Crie uma senha forte">
        </div>

        <div class="form-group">
            <label for="codigo_seguranca">Código de Autenticação Administrativa:</label>
            <input type="password" id="codigo_seguranca" name="codigo_seguranca" required placeholder="Chave do sistema">
        </div>

        <button type="submit" class="btn-cadastrar">Cadastrar como Coordenador</button>
    </form>
    
    <div class="link-login">
        Já possui conta? <a href="login_coordenador.php">Faça Login</a>
    </div>
</div>

</body>
</html>