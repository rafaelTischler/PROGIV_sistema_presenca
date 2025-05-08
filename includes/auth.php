<?php
require_once 'config.php';

function verificaLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

function registrarLog($acao, $modulo, $dados = null) {
    global $conn;
    
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    $dados = $dados ? json_encode($dados) : null;
    
    $stmt = $conn->prepare("INSERT INTO logs (usuario_id, acao, modulo, dados) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $usuario_id, $acao, $modulo, $dados);
    $stmt->execute();
    $stmt->close();
}

// Função para gerar token único
function gerarTokenUnico($tamanho = 32) {
    return bin2hex(random_bytes($tamanho));
}
?>