-- Sistema de Gestão para Salão Nil Sisters
-- Script SQL Básico (Apenas Estrutura)
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

    INDEX idx_cliente_ag (cliente_id),
    INDEX idx_servico_ag (servico_id),
    INDEX idx_funcionario_ag (funcionario_id),
    INDEX idx_data_ag (data_agendamento),
    INDEX idx_hora_ag (hora_agendamento),
    INDEX idx_status_ag (status),
    INDEX idx_data_cad_ag (data_cadastro),

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

    INDEX idx_tipo_fin (tipo),
    INDEX idx_data_fin (data),
    INDEX idx_categoria_fin (categoria),
    INDEX idx_data_cad_fin (data_cadastro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- FIM DO SCRIPT BÁSICO
-- =====================================================

SELECT 'Estrutura do banco criada com sucesso!' AS status;