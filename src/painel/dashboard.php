<?php
session_start();
require_once '../config/db.php';

// Verifica se existe ao menos 1 administrador cadastrado
$stmtAdmin = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE tipo = 'administrador'");
$stmtAdmin->execute();
$numAdmins = $stmtAdmin->fetchColumn();

if ($numAdmins == 0) {
    // Nenhum administrador cadastrado, redireciona para primeiro acesso
    header("Location: primeiro-acesso.php");
    exit;
}

// Verifica se está logado
if (!isset($_SESSION['cliente_id'])) {
    header("Location: /login.php");
    exit;
}

// Verifica se é administrador
if (!isset($_SESSION['cliente_tipo']) || $_SESSION['cliente_tipo'] !== 'administrador') {
    echo "Acesso negado. Área restrita para administradores.";
    exit;
}

$nome = $_SESSION['cliente_nome'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>Painel do Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Painel Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="/logout.php" class="nav-link btn btn-outline-danger btn-sm">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <div class="card shadow p-4">
            <h1>Bem-vindo, <?= htmlspecialchars($nome) ?>!</h1>
            <p>Você está no painel de administração.</p>

            <hr />

            <h3>Menu</h3>
            <ul>
                <li><a href="usuarios.php">Gerenciar Usuários</a></li>
                <li><a href="#">Relatórios</a></li>
                <li><a href="#">Configurações</a></li>
            </ul>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
