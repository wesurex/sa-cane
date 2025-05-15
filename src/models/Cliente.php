<?php
class Cliente {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Cadastrar novo cliente (com tipo)
    public function criar($dados) {
        $sql = "INSERT INTO clientes 
                (nome, email, telefone, senha, cep, rua, numero, complemento, bairro, cidade, estado, tipo)
                VALUES
                (:nome, :email, :telefone, :senha, :cep, :rua, :numero, :complemento, :bairro, :cidade, :estado, :tipo)";
        
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
            ':estado' => $dados['estado'],
            ':tipo' => $dados['tipo'] ?? 'cliente'  // caso não venha, padrão cliente
        ]);
    }

    // Buscar cliente por e-mail (login)
    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM clientes WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Definir token para recuperação de senha
    public function definirToken($email, $token, $expira) {
        $sql = "UPDATE clientes SET token_recuperacao = :token, token_expira_em = :expira WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':token' => $token,
            ':expira' => $expira,
            ':email' => $email
        ]);
    }

    // Redefinir a senha usando o token
    public function redefinirSenha($token, $novaSenha) {
        $sql = "SELECT * FROM clientes WHERE token_recuperacao = :token AND token_expira_em >= NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cliente) {
            $sql = "UPDATE clientes SET senha = :senha, token_recuperacao = NULL, token_expira_em = NULL WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':senha' => password_hash($novaSenha, PASSWORD_DEFAULT),
                ':id' => $cliente['id']
            ]);
        }

        return false;
    }
}
?>
