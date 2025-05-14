<?php
function getLocalIP()
{
    $ip = gethostbyname(gethostname());
    return (filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1');
}

if (!defined('NETWORK_CONFIG')) {
    define('NETWORK_CONFIG', [
        'ip' => getLocalIP(),
        'port' => '80',
        'protocol' => 'http'
    ]);
}

define('BASE_URL', NETWORK_CONFIG['protocol'] . '://' . NETWORK_CONFIG['ip'] . ':' . NETWORK_CONFIG['port'] . '/sistema-presenca/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_presenca');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SISTEMA_NOME', 'Sistema de Presença IFFar - SVS');
define('QRCODE_DIR', __DIR__ . '/../assets/qrcodes/');

function getServerIP()
{
    if (!empty($_SERVER['SERVER_ADDR'])) {
        return $_SERVER['SERVER_ADDR'];
    }
    return gethostbyname(gethostname());
}

if (!file_exists(QRCODE_DIR)) {
    mkdir(QRCODE_DIR, 0777, true);
}

session_start();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

require_once __DIR__ . '/../lib/phpqrcode/qrlib.php';

function redirect($url)
{
    header("Location: " . BASE_URL . $url);
    exit();
}
