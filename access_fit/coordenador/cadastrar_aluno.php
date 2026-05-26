<?php
session_start();
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../academia.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Aluno - Painel Administrativo</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; padding: 20px; }
        .card { background: white; padding: 30px; border-radius: 10px; shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        h2 { color: #d10e0e; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #555; }
        input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-salvar { width: 100%; padding: 12px; background: #d10e0e; color: white; border: none; border-radius: 5px; margin-top: 20px; cursor: pointer; font-size: 16px; }
        .btn-voltar { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Novo Aluno</h2>
        <form action="../actions/processa_coord_aluno.php" method="POST">
            <label>Nome Completo</label>
            <input type="text" name="nome" required>

            <label>E-mail (Login)</label>
            <input type="email" name="email" required>

            <label>Senha Provisória</label>
            <input type="password" name="senha" required>

            <label>CPF</label>
            <input type="text" name="cpf" maxlength="11" placeholder="Somente números" required>

            <label>Telefone</label>
            <input type="text" name="telefone" required>

            <label>Data de Nascimento</label>
            <input type="date" name="data_nasc" required>

            <label>Endereço</label>
            <input type="text" name="endereco" required>

            <label>Saúde/Alergias</label>
            <textarea name="historico_saude" rows="3" style="width: 100%; border: 1px solid #ddd; border-radius: 5px; padding: 10px;"></textarea>

            <button type="submit" class="btn-salvar">Finalizar Cadastro Administrativo</button>
            <a href="dashboard_coordenador.php" class="btn-voltar">Cancelar e Voltar</a>
        </form>
    </div>
</body>
</html>