<?php
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

$usuario_correto = 'admin@admin.com.br';
$senha_correta = '123456';

if ($email === $usuario_correto && $senha === $senha_correta) {
    // Simulando login com sucesso
    session_start();
    $_SESSION['usuario'] = $email;
    header('Location: dashboard.php');
    exit;
} else {
    echo '<script>alert("E-mail ou senha inválidos!"); history.back();</script>';
}
