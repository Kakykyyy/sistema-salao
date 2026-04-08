-- Sistema de Gestão para Salão Nil Sisters
-- Script SQL Completo
-- Versão: 2.0.0
-- Data: Abril 2026

-- =====================================================
-- CRIAÇÃO DO BANCO DE DADOS
-- =====================================================

CREATE DATABASE IF NOT EXISTS salao
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE salao;

-- =====================================================
-- TABELA: clientes
-- =====================================================

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    data_nascimento DATE NULL,
    observacoes TEXT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,

    -- Índices para performance
    INDEX idx_nome_cliente (nome),
    INDEX idx_telefone_cliente (telefone),
    INDEX idx_email_cliente (email),
    INDEX idx_ativo_cliente (ativo),
    INDEX idx_data_cadastro_cliente (data_cadastro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: funcionarios
-- =====================================================

CREATE TABLE IF NOT EXISTS funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    comissao_padrao DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,

    -- Índices para performance
    INDEX idx_nome_func (nome),
    INDEX idx_email_func (email),
    INDEX idx_ativo_func (ativo),
    INDEX idx_data_cad_func (data_cadastro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: servicos
-- =====================================================

CREATE TABLE IF NOT EXISTS servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    descricao TEXT NULL,
    duracao VARCHAR(50) NULL,
    preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    comissao DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo TINYINT(1) DEFAULT 1,

    -- Índices para performance
    INDEX idx_nome_servico (nome),
    INDEX idx_preco (preco),
    INDEX idx_ativo_serv (ativo),
    INDEX idx_data_cad_serv (data_cadastro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: agendamentos
-- =====================================================

CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    servico_id INT NOT NULL,
    funcionario_id INT NOT NULL,
    data_agendamento DATE NOT NULL,
    hora_agendamento TIME NOT NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    comissao DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('agendado', 'concluido', 'cancelado') DEFAULT 'agendado',
    observacoes TEXT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Índices para performance
    INDEX idx_cliente_ag (cliente_id),
    INDEX idx_servico_ag (servico_id),
    INDEX idx_funcionario_ag (funcionario_id),
    INDEX idx_data_ag (data_agendamento),
    INDEX idx_hora_ag (hora_agendamento),
    INDEX idx_status_ag (status),
    INDEX idx_data_cad_ag (data_cadastro),

    -- Chaves estrangeiras
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE CASCADE,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: financeiro
-- =====================================================

CREATE TABLE IF NOT EXISTS financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    categoria VARCHAR(100) NULL,
    valor DECIMAL(10,2) NOT NULL,
    tipo ENUM('entrada', 'saida') NOT NULL,
    data DATE NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Índices para performance
    INDEX idx_tipo_fin (tipo),
    INDEX idx_data_fin (data),
    INDEX idx_categoria_fin (categoria),
    INDEX idx_data_cad_fin (data_cadastro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DADOS INICIAIS PARA TESTE
-- =====================================================

-- Inserir funcionários de exemplo
INSERT IGNORE INTO funcionarios (nome, telefone, email, comissao_padrao, ativo) VALUES
('Maria Silva', '(11) 99999-0001', 'maria@salao.com', 10.00, 1),
('João Santos', '(11) 99999-0002', 'joao@salao.com', 12.00, 1),
('Ana Costa', '(11) 99999-0003', 'ana@salao.com', 8.00, 1);

-- Inserir serviços de exemplo
INSERT IGNORE INTO servicos (nome, descricao, duracao, preco, comissao, ativo) VALUES
('Corte de Cabelo Feminino', 'Corte moderno com acabamento profissional', '1h', 45.00, 15.00, 1),
('Corte de Cabelo Masculino', 'Corte clássico ou moderno', '45min', 30.00, 12.00, 1),
('Coloração Completa', 'Coloração com descoloração incluída', '2h30min', 120.00, 20.00, 1),
('Manicure', 'Cuidados completos das unhas', '1h', 25.00, 10.00, 1),
('Pedicure', 'Cuidados completos dos pés', '1h15min', 35.00, 12.00, 1),
('Massagem Relaxante', 'Massagem terapêutica de 50 minutos', '50min', 60.00, 18.00, 1),
('Hidratação Capilar', 'Tratamento intensivo para cabelos danificados', '1h30min', 80.00, 15.00, 1),
('Limpeza de Pele', 'Limpeza profunda com extração de cravos', '1h', 70.00, 16.00, 1);

-- Inserir clientes de exemplo
INSERT IGNORE INTO clientes (nome, telefone, email, data_nascimento, ativo) VALUES
('Carla Oliveira', '(11) 98888-0001', 'carla@email.com', '1990-05-15', 1),
('Roberto Almeida',  '(11) 98888-0002', 'roberto@email.com', '1985-08-22', 1),
('Fernanda Lima',  '(11) 98888-0003', 'fernanda@email.com', '1992-12-10', 1),
('Carlos Mendes','(11) 98888-0004', 'carlos@email.com', '1988-03-28', 1),
('Juliana Santos', '(11) 98888-0005', 'juliana@email.com', '1995-07-14', 1);

-- Inserir lançamentos financeiros de exemplo
INSERT IGNORE INTO financeiro (descricao, categoria, valor, tipo, data) VALUES
('Venda de produtos', 'Produtos', 150.00, 'entrada', CURDATE()),
('Pagamento de luz', 'Contas', 120.00, 'saida', CURDATE()),
('Aluguel do salão', 'Aluguel', 800.00, 'saida', DATE_SUB(CURDATE(), INTERVAL 1 MONTH)),
('Compra de tintas', 'Insumos', 200.00, 'saida', DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
('Serviços prestados', 'Serviços', 350.00, 'entrada', DATE_SUB(CURDATE(), INTERVAL 1 DAY));

-- =====================================================
-- AGENDAMENTOS DE EXEMPLO
-- =====================================================

-- Agendamento futuro
INSERT IGNORE INTO agendamentos (cliente_id, servico_id, funcionario_id, data_agendamento, hora_agendamento, valor, comissao, status, observacoes) VALUES
(1, 1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', 45.00, 6.75, 'agendado', 'Cliente prefere corte mais curto'),
(2, 2, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:30:00', 30.00, 3.60, 'agendado', 'Primeira visita'),
(3, 4, 3, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '16:00:00', 25.00, 2.50, 'agendado', NULL);

-- Agendamento passado (concluído)
INSERT IGNORE INTO agendamentos (cliente_id, servico_id, funcionario_id, data_agendamento, hora_agendamento, valor, comissao, status, observacoes, data_cadastro) VALUES
(4, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '15:00:00', 45.00, 6.75, 'concluido', 'Cliente satisfeita', DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
(5, 5, 2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '11:00:00', 35.00, 4.20, 'concluido', NULL, DATE_SUB(CURDATE(), INTERVAL 1 DAY));

-- =====================================================
-- VIEWS ÚTEIS PARA RELATÓRIOS
-- =====================================================

-- View para relatório de agendamentos completos
CREATE OR REPLACE VIEW vw_agendamentos_completos AS
SELECT
    a.id,
    a.data_agendamento,
    a.hora_agendamento,
    a.valor,
    a.comissao,
    a.status,
    a.observacoes,
    a.data_cadastro,
    c.nome AS cliente_nome,
    c.telefone AS cliente_telefone,
    c.email AS cliente_email,
    s.nome AS servico_nome,
    s.duracao AS servico_duracao,
    f.nome AS funcionario_nome,
    f.telefone AS funcionario_telefone
FROM agendamentos a
JOIN clientes c ON a.cliente_id = c.id
JOIN servicos s ON a.servico_id = s.id
JOIN funcionarios f ON a.funcionario_id = f.id;

-- View para relatório financeiro mensal
CREATE OR REPLACE VIEW vw_financeiro_mensal AS
SELECT
    DATE_FORMAT(data, '%Y-%m') AS mes_ano,
    tipo,
    SUM(valor) AS total_valor,
    COUNT(*) AS quantidade_lancamentos
FROM financeiro
GROUP BY DATE_FORMAT(data, '%Y-%m'), tipo
ORDER BY mes_ano DESC, tipo;

-- View para relatório de comissões
CREATE OR REPLACE VIEW vw_relatorio_comissoes AS
SELECT
    f.id AS funcionario_id,
    f.nome AS funcionario_nome,
    DATE_FORMAT(a.data_agendamento, '%Y-%m') AS mes_ano,
    COUNT(a.id) AS total_servicos,
    SUM(a.valor) AS valor_total_servicos,
    SUM(a.comissao) AS comissao_total,
    AVG(a.comissao) AS comissao_media
FROM funcionarios f
LEFT JOIN agendamentos a ON f.id = a.funcionario_id
    AND a.status = 'concluido'
    AND a.data_agendamento >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
WHERE f.ativo = 1
GROUP BY f.id, f.nome, DATE_FORMAT(a.data_agendamento, '%Y-%m')
ORDER BY f.nome, mes_ano DESC;

-- =====================================================
-- PROCEDURES ÚTEIS
-- =====================================================

-- Procedure para calcular comissão total de um funcionário em um período
DELIMITER //

CREATE PROCEDURE sp_calcular_comissao_funcionario(
    IN p_funcionario_id INT,
    IN p_data_inicio DATE,
    IN p_data_fim DATE
)
BEGIN
    SELECT
        f.nome AS funcionario,
        COUNT(a.id) AS servicos_realizados,
        SUM(a.valor) AS valor_total,
        SUM(a.comissao) AS comissao_total,
        AVG(a.comissao) AS comissao_media
    FROM funcionarios f
    LEFT JOIN agendamentos a ON f.id = a.funcionario_id
        AND a.status = 'concluido'
        AND a.data_agendamento BETWEEN p_data_inicio AND p_data_fim
    WHERE f.id = p_funcionario_id
        AND f.ativo = 1
    GROUP BY f.id, f.nome;
END //

-- Procedure para relatório financeiro por período
CREATE PROCEDURE sp_relatorio_financeiro(
    IN p_data_inicio DATE,
    IN p_data_fim DATE
)
BEGIN
    SELECT
        tipo,
        SUM(valor) AS total,
        COUNT(*) AS quantidade,
        AVG(valor) AS media
    FROM financeiro
    WHERE data BETWEEN p_data_inicio AND p_data_fim
    GROUP BY tipo
    ORDER BY tipo;
END //

DELIMITER ;

-- =====================================================
-- TRIGGERS PARA MANTER INTEGRIDADE
-- =====================================================

-- Trigger para atualizar valor e comissão do agendamento quando o serviço muda
DELIMITER //

CREATE TRIGGER trg_atualizar_agendamento_servico
    BEFORE UPDATE ON servicos
    FOR EACH ROW
BEGIN
    IF OLD.preco != NEW.preco OR OLD.comissao != NEW.comissao THEN
        UPDATE agendamentos
        SET valor = NEW.preco,
            comissao = (NEW.preco * NEW.comissao / 100)
        WHERE servico_id = NEW.id
            AND status = 'agendado';
    END IF;
END //

DELIMITER ;

-- =====================================================
-- USUÁRIO PARA APLICAÇÃO (OPCIONAL)
-- =====================================================

-- Criar usuário específico para a aplicação (descomente se necessário)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON salao.* TO 'salao_user'@'localhost' IDENTIFIED BY 'senha_segura_aqui';
-- FLUSH PRIVILEGES;

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================

-- Verificar se tudo foi criado corretamente
SELECT 'Banco de dados criado com sucesso!' AS status;