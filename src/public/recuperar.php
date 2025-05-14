<?php
require_once '../config/db.php';
require_once '../models/Cliente.php';
require_once '../services/Mailer.php';
require_once '../config/env.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = new Cliente($pdo);
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $url = ($_ENV['APP_URL'] ?? 'http://localhost:8080') . "/public/reset-senha.php/$token";

    if ($cliente->definirToken($email, $token, $expira)) {
        $html = "<p>Olá! Clique no link abaixo para redefinir sua senha:</p><p><a href='$url'>$url</a></p>";
        Mailer::enviar($email, "Recuperação de Senha", $html);
        $mensagem = "Um link de recuperação foi enviado para seu e-mail.";
    } else {
        $mensagem = "E-mail não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Recuperar Senha</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
    <h4 class="mb-3">Recuperar Senha</h4>
    <?php if ($mensagem): ?>
      <div class="alert alert-info"><?= $mensagem ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3"><label class="form-label">Seu e-mail</label><input type="email" class="form-control" name="email" required></div>
      <button type="submit" class="btn btn-primary w-100">Enviar link</button>
    </form>
  </div>
</div>
</body>
</html>