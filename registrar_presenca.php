<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isset($_GET['id'])) {
    $reuniao_id = intval($_GET['id']);

    // Verifica se a reunião existe
    $reuniao = $conn->query("SELECT id, titulo FROM reunioes WHERE id = $reuniao_id")->fetch_assoc();

    if ($reuniao && isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];

        // Verifica se já está registrado
        $jaRegistrado = $conn->query("
            SELECT id FROM presencas 
            WHERE usuario_id = $usuario_id AND reuniao_id = $reuniao_id
        ")->num_rows;

        if (!$jaRegistrado) {
            $conn->query("INSERT INTO presencas (usuario_id, reuniao_id) VALUES ($usuario_id, $reuniao_id)");
            $_SESSION['sucesso'] = "Presença confirmada em: " . $reuniao['titulo'];
        } else {
            $_SESSION['aviso'] = "Presença já registrada anteriormente";
        }
    } else {
        $_SESSION['erro'] = "Reunião inválida ou usuário não logado";
    }
}

redirect(isset($_SESSION['usuario_id']) ? 'servidor/minhas-presencas/' : 'login.php');
