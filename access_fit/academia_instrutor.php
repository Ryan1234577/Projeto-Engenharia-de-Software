<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['usuario_id']) && isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'instrutor') {
    header("Location: instrutor/dashboard_instrutor.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Instrutor - Access Fit</title>
    <style>
        body {
            background: linear-gradient(135deg, #4e0000 0%, #000000 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .container-login {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 350px;
            text-align: center;
        }
        h2 { color: #333; margin-bottom: 20px; }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn-entrar {
            background-color: #cc0000;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
        }
        .btn-entrar:hover { background-color: #990000; }
        
        
        .area-cadastro {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 14px;
        }
        .area-cadastro a {
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }
        .area-cadastro a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container-login">
    <h2>Access Fit - Instrutor</h2>
    
    <form action="actions/login.php" method="POST">
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit" class="btn-entrar">Entrar</button>
    </form>

    <div class="area-cadastro">
        <p>Ainda não tem uma conta?</p>
        <a href="instrutor/cadastro_instrutor.php">Criar Novo Cadastro</a>
    </div>
</div>

</body>
</html>