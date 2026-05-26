<?php
session_start();
include('../config/config.php');


if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'coordenador') {
    header("Location: ../login.php");
    exit();
}


if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];

    $stmt = $conn->prepare("UPDATE alunos SET status = 'inativo' WHERE usuario_id = ?");
    $stmt->bind_param("i", $id_usuario);

    if ($stmt->execute()) {
        header("Location: dashboard_coordenador.php?status=inativado");
    } else {
        die("Erro ao processar a inativação no banco de dados.");
    }
    exit();
} else {
    header("Location: dashboard_coordenador.php");
    exit();
}
?>