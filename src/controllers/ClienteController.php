<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar($dados) {
        // Garante que o tipo seja sempre 'cliente'
        if (!isset($dados['tipo'])) {
            $dados['tipo'] = 'cliente';
        }

        $cliente = new Cliente($this->pdo);
        $cliente->criar($dados);
        header('Location: login.php');
        exit;
    }
}
?>
