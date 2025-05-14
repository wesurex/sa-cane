<?php
require_once '../config/db.php';
require_once '../models/Cliente.php';
require_once '../config/env.php';

$cliente = new Cliente($pdo);

$uri = $_SERVER['REQUEST_URI'];
$partes = explode('/', $uri);
$token = end($partes);

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = $_POST['nova_senha'];
    $confirmar = $_POST['confirmar_senha'];

    if ($nova !== $confirmar) {
        $mensagem = "As senhas não coincidem.";
    } else {
        if ($cliente->redefinirSenha($token, $nova)) {
            $mensagem = "Senha redefinida com sucesso! <a href='login.php'>Fazer login</a>";
        } else {
            $mensagem = "Token inválido ou expirado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Redefinir Senha</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
    <h4 class="mb-3">Redefinir Senha</h4>
    <?php if ($mensagem): ?>
      <div class="alert alert-info"><?= $mensagem ?></div>
    <?php endif; ?>
    <?php if (!$mensagem || str_contains($mensagem, 'coincidem')): ?>
    <form method="POST">
      <div class="mb-3"><label class="form-label">Nova senha</label><input type="password" class="form-control" name="nova_senha" required></div>
      <div class="mb-3"><label class="form-label">Confirmar senha</label><input type="password" class="form-control" name="confirmar_senha" required></div>
      <button class="btn btn-primary w-100">Salvar nova senha</button>
    </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>