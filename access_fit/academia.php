<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Access Fit - Área do Aluno</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #000, #d10e0e); height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0; }
        .login-card { background: #fff; padding: 40px; border-radius: 30px; width: 100%; max-width: 350px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        h2 { margin-bottom: 25px; color: #333; font-size: 28px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; font-size: 16px; }
        button { width: 100%; padding: 14px; background-color: #d10e0e; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 18px; font-weight: bold; margin-top: 15px; }
        .links { margin-top: 15px; font-size: 14px; color: #0056b3; text-decoration: none; display: block; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Área do Aluno</h2>
        <form action="actions/login.php" method="POST">
            <input type="hidden" name="area_acessada" value="aluno">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
        <a href="novo_cadastro.html" class="links">Sou novo no pedaço (Cadastrar)</a>
    </div>
</body>
</html>