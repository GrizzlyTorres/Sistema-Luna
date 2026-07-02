CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    token VARCHAR(255) NULL
);

-- Tabela de Pacientes
CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    data_nascimento DATE NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    endereco VARCHAR(255),
    naturalidade VARCHAR(100),
    escolaridade VARCHAR(50),
    estado_civil VARCHAR(50),
    religiao VARCHAR(50),
    indicacao VARCHAR(255),
    observacoes TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Agendamentos
CREATE TABLE agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_info VARCHAR(255) NOT NULL, -- Pode ser Nome ou CPF
    data_consulta DATE NOT NULL,
    horario TIME NOT NULL,
    tipo_atendimento VARCHAR(50),
    informacoes TEXT,
    status VARCHAR(20) DEFAULT 'agendado',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
