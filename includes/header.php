<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SISTEMA_NOME ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .container { width: 90%; margin: auto; padding: 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #3c763d; background-color: #dff0d8; border-color: #d6e9c6; }
        .alert-danger { color: #a94442; background-color: #f2dede; border-color: #ebccd1; }
        .form-group { margin-bottom: 15px; }
        .form-control { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 8px 15px; background: #337ab7; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #286090; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= SISTEMA_NOME ?></h1>
        <?php if (isset($_SESSION['usuario_nome'])): ?>
            <p>Bem-vindo, <?= $_SESSION['usuario_nome'] ?>! 
               <a href="<?= BASE_URL ?>logout.php">(Sair)</a></p>
        <?php endif; ?>
        <hr>