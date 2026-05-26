<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Instrutor - Access Fit</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #4e0000 0%, #000000 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0; padding: 20px; }
        .card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); width: 100%; max-width: 400px; box-sizing: border-box; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        label { font-weight: bold; font-size: 14px; color: #555; display: block; margin-top: 12px; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn-submit { background-color: #cc0000; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; cursor: pointer; font-weight: bold; margin-top: 20px; font-size: 16px; }
        .btn-submit:hover { background-color: #990000; }
        .back-link { text-align: center; margin-top: 15px; display: block; color: #0056b3; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Cadastrar Novo Instrutor</h2>
    <form action="../actions/processa_cadastro_instrutor.php" method="POST">
        <label>Nome Completo</label>
        <input type="text" name="nome" placeholder="Nome do Instrutor" required>

        <label>E-mail</label>
        <input type="email" name="email" placeholder="email@accessfit.com" required>

        <label>CREF</label>
        <input type="text" name="cref" placeholder="000000-G/SP" required>

        <label>Telefone</label>
        <input type="text" name="telefone" placeholder="11912345678" required>

        <label>Especialidade</label>
        <select name="especialidade" required>
            <option value="Musculação">Musculação</option>
            <option value="Cardio / Corrida">Cardio / Corrida</option>
            <option value="Crossfit">Crossfit</option>
            <option value="Pilates">Pilates</option>
        </select>

        <label>Senha Provisória</label>
        <input type="password" name="senha" placeholder="Digite uma senha" required>

        <label>Link da Foto de Perfil (Opcional)</label>
        <input type="url" name="foto_url" placeholder="https://linkdafoto.com/imagem.jpg">

        <button type="submit" class="btn-submit">Finalizar Cadastro</button>
    </form>
    
    <a href="../academia_instrutor.php" class="back-link">Voltar para o Login</a>
</div>

</body>
</html>