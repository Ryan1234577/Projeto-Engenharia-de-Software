<?php
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = array();

// Destrói a sessão
session_destroy();

// Se o seu ficheiro principal na raiz for index.html:
header("Location: ../index.html");

// SE o seu ficheiro for index.htm (sem o 'l'), use esta linha:
// header("Location: ../index.htm");

exit();
?>