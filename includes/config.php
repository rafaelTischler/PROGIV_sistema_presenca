<?php
// Função para obter o IP local (opcional)
function getLocalIP() {
    $ip = gethostbyname(gethostname());
    return (filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1');
}

// Definindo as configurações de rede (IP, Porta, Protocolo)
if (!defined('NETWORK_CONFIG')) {
    define('NETWORK_CONFIG', [
        'ip' => getLocalIP(),
        'port' => '80',
        'protocol' => 'http'
    ]);
}

// Constante para URL base dinâmica
define('BASE_URL', NETWORK_CONFIG['protocol'].'://'.NETWORK_CONFIG['ip'].':'.NETWORK_CONFIG['port'].'/sistema-presenca/');

// Configurações de banco de dados
define('DB_HOST', 'localhost'); 
define('DB_NAME', 'sistema_presenca');
define('DB_USER', 'root');
define('DB_PASS', '');

// Função para obter o IP do servidor
function getServerIP() {
    if (!empty($_SERVER['SERVER_ADDR'])) {
        return $_SERVER['SERVER_ADDR'];
    }
    return gethostbyname(gethostname());
}

// Configurações do sistema
define('SISTEMA_NOME', 'Sistema de Presença IFFar - SVS');
define('QRCODE_DIR', __DIR__ . '/../assets/qrcodes/');

// Cria a pasta de QR Codes se não existir
if (!file_exists(QRCODE_DIR)) {
    mkdir(QRCODE_DIR, 0777, true);
}

// Inicia a sessão
session_start();

// Conexão com o banco de dados
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verifica erro de conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Inclusão da biblioteca de QR Code (caso seja necessário)
require_once __DIR__ . '/../lib/phpqrcode/qrlib.php';

// Função para redirecionamento
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}
?>
