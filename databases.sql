-- Criação do banco
CREATE DATABASE IF NOT EXISTS agenda_ai DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE agenda_ai;

-- Usuários internos do sistema (admin e usuários da agenda)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin', 'usuario') DEFAULT 'usuario',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Clientes (usuário final que agenda)
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    senha VARCHAR(255) NOT NULL,

    cep VARCHAR(9),
    rua VARCHAR(100),
    numero VARCHAR(10),
    complemento VARCHAR(50),
    bairro VARCHAR(50),
    cidade VARCHAR(100),
    estado VARCHAR(2),

    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Configuração de horários disponíveis por dia da semana
CREATE TABLE horarios_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    dia_semana ENUM('segunda','terca','quarta','quinta','sexta','sabado','domingo') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Agendamentos feitos por clientes com os usuários internos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cliente_id INT NOT NULL,
    data DATE NOT NULL,
    hora TIME NOT NULL,
    status ENUM('agendado', 'cancelado') DEFAULT 'agendado',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY (user_id, data, hora), -- impede agendamento duplicado no mesmo horário
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

-- Adicionando o campo tipo na tabela clientes
ALTER TABLE clientes ADD COLUMN tipo ENUM('administrador', 'cliente', 'cabeleireiro') DEFAULT 'cliente';
