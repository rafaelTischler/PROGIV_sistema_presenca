<?php
require_once 'includes/config.php';

// Registrar log de logout
if (isset($_SESSION['usuario_id'])) {
    $conn->query("INSERT INTO logs (usuario_id, acao, modulo) VALUES ({$_SESSION['usuario_id']}, 'logout', 'sistema')");
}

session_destroy();
redirect('login.php');