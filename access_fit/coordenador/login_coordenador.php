<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Coordenador - Access Fit</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #111; /* Fundo escuro igual ao tema principal */
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #1a1a1a;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(255, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            border-top: 4px solid #ff0000; /* Detalhe em vermelho */
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
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-bottom: 8px;
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
        .btn-login {
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
        .btn-login:hover {
            background: #b30000;
        }
        .link-cadastro {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #ccc;
        }
        .link-cadastro a {
            color: #ff0000;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .link-cadastro a:hover {
            color: #ff6666;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Access Fit</h2>
    <p>Painel Administrativo - Login do Coordenador</p>

    <form action="../actions/processa_login.php" method="POST">
        <input type="hidden" name="area_acessada" value="coordenador">

        <div class="form-group">
            <label for="email">E-mail de Acesso:</label>
            <input type="email" id="email" name="email" required placeholder="coordenador@accessfit.com">
        </div>

        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required placeholder="Sua senha secreta">
        </div>

        <button type="submit" class="btn-login">Entrar no Sistema</button>
    </form>
    
    <div class="link-cadastro">
        Ainda não tem acesso? <a href="cadastro_coordenador.php">Cadastre-se aqui</a>
    </div>
</div>

</body>
</html>