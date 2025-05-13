<?php

// Incluir a configuração antes de qualquer outra coisa
require_once 'config.php';

// Garantir que o código de debug seja removido ou comentado em produção
// echo "entrou"; // Remover em produção

// Função para verificar se o usuário está logado
function verificaLogin() {
    // Verifica se a sessão do usuário está ativa
    if (!isset($_SESSION['usuario_id'])) {
        // Armazena a URL atual para redirecionamento após login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];

        // Redireciona para a página de login, utilizando a BASE_URL
        redirect('login.php'); // Caminho relativo com BASE_URL
    }
}

// Função para verificar se o usuário é administrador
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

// Função para registrar as ações no log
function registrarLog($acao, $modulo, $dados = null) {
    global $conn;

    // Obtém o ID do usuário
    $usuario_id = $_SESSION['usuario_id'] ?? null;

    // Se houver dados adicionais, codifica em formato JSON
    $dados = $dados ? json_encode($dados) : null;

    // Prepara a consulta SQL para registrar no banco
    $stmt = $conn->prepare("INSERT INTO logs (usuario_id, acao, modulo, dados) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $usuario_id, $acao, $modulo, $dados);
    $stmt->execute();  // Executa a consulta
    $stmt->close();    // Fecha a preparação da consulta
}

// Função para gerar um token único (por exemplo, para CSRF ou sessões)
function gerarTokenUnico($tamanho = 32) {
    return bin2hex(random_bytes($tamanho)); // Gera um token seguro
}
