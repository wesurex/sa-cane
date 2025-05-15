<?php
session_start();
require_once '../config/db.php';

// Somente admins podem acessar
if (!isset($_SESSION['cliente_id']) || $_SESSION['cliente_tipo'] !== 'administrador') {
    http_response_code(403);
    exit('Acesso negado');
}

$q = $_GET['q'] ?? '';
$q = trim($q);

if ($q === '') {
    $stmt = $pdo->query("SELECT id, nome, email, tipo FROM clientes ORDER BY id DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("SELECT id, nome, email, tipo FROM clientes WHERE nome LIKE :busca OR email LIKE :busca ORDER BY id DESC");
    $busca = "%$q%";
    $stmt->execute([':busca' => $busca]);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($usuarios);
