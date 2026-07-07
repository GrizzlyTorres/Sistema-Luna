CREATE DATABASE clinica_luna;
use clinica_luna;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    token VARCHAR(255) NULL
);

-- Tabela de Pacientes (Atualizada com genero e sexo)
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
    genero VARCHAR(50), -- NOVO CAMPO
    sexo VARCHAR(50),   -- NOVO CAMPO
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

INSERT INTO pacientes (nome, data_nascimento, cpf, telefone, email, endereco, naturalidade, escolaridade, estado_civil, religiao, indicacao, observacoes, genero, sexo) 
VALUES 
('Ana Clara Mendes', '1990-05-14', '111.222.333-44', '(21) 98888-1111', 'ana.mendes@email.com', 'Rua Carvalho de Souza, 100 - Madureira, RJ', 'Rio de Janeiro, RJ', 'superior', 'solteiro', 'Católica', 'Instagram', 'Paciente relata ansiedade leve devido ao trabalho.', 'mulher-cis', 'feminino'),

('Roberto Alves', '1985-11-22', '555.666.777-88', '(21) 97777-2222', 'roberto.alves@email.com', 'Estrada de Botafogo, 250 - Pavuna, RJ', 'Rio de Janeiro, RJ', 'medio', 'casado', 'Evangélica', 'Dr. Marcos (Psiquiatra)', 'Primeira sessão. Foco em transição de carreira.', 'homem-cis', 'masculino'),

('Alex Ferreira', '1998-03-10', '999.888.777-66', '(21) 96666-3333', 'alex.ferreira@email.com', 'Av. Brás de Pina, 500 - Penha Circular, RJ', 'São Paulo, SP', 'superior', 'solteiro', 'Nenhuma', 'Busca no Google', 'Iniciando acompanhamento semanal.', 'nao-binario', 'intersexo'),

('Luiza Fontes', '2001-08-05', '444.555.666-77', '(21) 95555-4444', 'luiza.fontes@email.com', 'Rua Carolina Machado, 300 - Madureira, RJ', 'Belo Horizonte, MG', 'superior', 'casado', 'Espírita', 'Indicação de amiga', 'Sessões focadas em autoestima e autoconhecimento.', 'mulher-trans', 'masculino');

INSERT INTO agendamentos 
(paciente_info, data_consulta, horario, tipo_atendimento, informacoes, status) 
VALUES 
-- Uma consulta para HOJE (Para testar a aba "Hoje" do Dashboard)
('Ana Clara Mendes - 111.222.333-44', CURDATE(), '14:00:00', 'pacote', 'Sessão 1 de 4 do pacote mensal.', 'agendado'),

-- Uma consulta para o dia SEGUINTE (Para testar a aba "Próximos")
('Roberto Alves - 555.666.777-88', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', 'avulso', 'Primeira avaliação psicológica.', 'agendado'),

-- Uma consulta para a PRÓXIMA SEMANA
('Alex Ferreira - 999.888.777-66', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '16:00:00', 'pacote', 'Acompanhamento regular de rotina.', 'agendado'),

-- Uma consulta do PASSADO já CONCLUÍDA (Para testar o histórico/recentes)
('Luiza Fontes - 444.555.666-77', DATE_SUB(CURDATE(), INTERVAL 3 DAY), '09:00:00', 'avulso', 'Sessão inicial finalizada com sucesso.', 'concluido');