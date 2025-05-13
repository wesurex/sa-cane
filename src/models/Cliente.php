<?php
class Cliente {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function criar($dados) {
        $sql = "INSERT INTO clientes (nome, email, telefone, senha, cep, rua, numero, complemento, bairro, cidade, estado)
                VALUES (:nome, :email, :telefone, :senha, :cep, :rua, :numero, :complemento, :bairro, :cidade, :estado)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'],
            ':email' => $dados['email'],
            ':telefone' => $dados['telefone'],
            ':senha' => password_hash($dados['senha'], PASSWORD_DEFAULT),
            ':cep' => $dados['cep'],
            ':rua' => $dados['rua'],
            ':numero' => $dados['numero'],
            ':complemento' => $dados['complemento'],
            ':bairro' => $dados['bairro'],
            ':cidade' => $dados['cidade'],
            ':estado' => $dados['estado']
        ]);
    }
}
?>