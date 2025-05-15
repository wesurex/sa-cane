<?php

class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($dados) {
        $email = $dados['email'] ?? '';
        $senha = $dados['senha'] ?? '';

        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente && password_verify($senha, $cliente['senha'])) {
            // Segurança: Regenera o ID da sessão
            session_regenerate_id(true);

            // Salva dados na sessão
            $_SESSION['cliente_id'] = $cliente['id'];
            $_SESSION['cliente_nome'] = $cliente['nome'];
            $_SESSION['cliente_tipo'] = $cliente['tipo'];

            // Redireciona conforme tipo
            if ($cliente['tipo'] === 'administrador') {
                header("Location: /painel/dashboard.php");
                exit;
            } elseif ($cliente['tipo'] === 'cabeleireiro') {
                header("Location: /public/index.php");
                exit;
            } else {
                // tipo cliente ou outros
                header("Location: /public/index.php");
                exit;
            }
        } else {
            return "E-mail ou senha inválidos.";
        }
    }
}