<?php
session_start();


if (!file_exists('../config/config.php')) {
    die("Erro interno do sistema. Arquivo de configuração não encontrado.");
}
include('../config/config.php');


if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'instrutor') {
    header("Location: ../login.php");
    exit();
}

$aluno_id = isset($_GET['aluno_id']) ? intval($_GET['aluno_id']) : 0;
$inst_id  = isset($_GET['inst_id']) ? intval($_GET['inst_id']) : 0;

if ($aluno_id > 0 && $inst_id > 0) {
    $stmt = $conn->prepare("UPDATE alunos SET instrutor_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $inst_id, $aluno_id);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Aluno adotado com sucesso! Agora ele aparecerá na sua lista técnica.');
                window.location.href = '../instrutor/dashboard_instrutor.php';
              </script>";
    } else {
        echo "<script>
                alert('Erro ao processar o vínculo no banco de dados.');
                window.history.back();
              </script>";
    }
    
    $stmt->close();
} else {
    echo "<script>
            alert('Parâmetros inválidos ou ausentes para a operação.');
            window.history.back();
          </script>";
}

$conn->close();
?>