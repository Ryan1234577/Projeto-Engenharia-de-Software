<?php
require_once('../config/config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id   = $_POST['aluno_id'];
    $usuario_id = $_POST['usuario_id'];
    $nome       = $_POST['nome'];
    $email      = $_POST['email'];
    $telefone   = $_POST['telefone'];
    $endereco   = $_POST['endereco'];
    $saude      = $_POST['historico_saude'];

    $conn->begin_transaction();

    try {
        
        $stmt1 = $conn->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
        $stmt1->bind_param("ssi", $nome, $email, $usuario_id);
        $stmt1->execute();

        
        $stmt2 = $conn->prepare("UPDATE alunos SET nome = ?, telefone = ?, endereco = ?, historico_saude = ? WHERE id = ?");
        $stmt2->bind_param("ssssi", $nome, $telefone, $endereco, $saude, $aluno_id);
        $stmt2->execute();

        $conn->commit();
        echo "<script>alert('Aluno atualizado com sucesso!'); window.location.href='../coordenador/dashboard_coordenador.php';</script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "Erro ao atualizar: " . $e->getMessage();
    }
}