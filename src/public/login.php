<?php
session_start();
require_once '../config/db.php';
require_once '../controllers/ClienteController.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteController = new ClienteController($pdo);
    $erro = $clienteController->login($_POST);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
    <h3 class="text-center mb-4">Login</h3>
    <?php if ($erro): ?>
      <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" name="email" required></div>
      <div class="mb-3"><label class="form-label">Senha</label><input type="password" class="form-control" name="senha" required></div>
      <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
    <div class="text-center mt-3"><a href="recuperar.php">Esqueci minha senha</a></div>
  </div>
</div>
</body>
</html>