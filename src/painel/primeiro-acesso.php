<?php
session_start();
require_once '../config/db.php';

$stmtAdmin = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE tipo = 'administrador'");
$stmtAdmin->execute();
$numAdmins = $stmtAdmin->fetchColumn();

if ($numAdmins > 0) {
    // Já tem admin cadastrado, redireciona para login ou painel
    header("Location: /public/login.php"); // Ou para painel.php, conforme seu fluxo
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmar) {
        $erro = 'As senhas não coincidem.';
    } else {
        // Verifica se já existe um usuário com esse email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clientes WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $existe = $stmt->fetchColumn();

        if ($existe > 0) {
            $erro = 'Esse e-mail já está em uso.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
            $stmt->execute([
                ':nome' => 'Administrador',
                ':email' => $email,
                ':senha' => $hash,
                ':tipo' => 'administrador'
            ]);

            header("Location: /public/login.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Primeiro Acesso - Criar Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4">Criar Administrador</h3>
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" class="form-control" name="senha" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmar Senha</label>
                <input type="password" class="form-control" name="confirmar" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Criar Administrador</button>
        </form>
    </div>
</div>
</body>
</html>