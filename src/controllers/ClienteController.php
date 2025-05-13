<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar($dados) {
        $cliente = new Cliente($this->pdo);
        $cliente->criar($dados);
        header('Location: login.php');
        exit;
    }
}
?>