-- Backup do banco de dados salao
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `telefone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clientes` (`id`, `nome`, `telefone`, `email`) VALUES ('1', 'Kayky', '11 98925662', 'kaykyrck@gmail.com');

DROP TABLE IF EXISTS `servicos`;
CREATE TABLE `servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `duracao` varchar(255) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `servicos` (`id`, `nome`, `duracao`, `preco`) VALUES ('1', 'Manicure ', '60', '150.00');

DROP TABLE IF EXISTS `funcionarios`;
CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `comissao` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `funcionarios` (`id`, `nome`, `comissao`) VALUES ('1', 'Thaaina', '0.00');

DROP TABLE IF EXISTS `agendamentos`;
CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `funcionario_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `desconto` decimal(5,2) NOT NULL DEFAULT 0.00,
  `comissao` decimal(5,2) NOT NULL DEFAULT 0.00,
  `data` date NOT NULL,
  `hora` time NOT NULL,
  `observacao` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `servico_id` (`servico_id`),
  KEY `funcionario_id` (`funcionario_id`),
  CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`),
  CONSTRAINT `agendamentos_ibfk_3` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `agendamentos` (`id`, `cliente_id`, `servico_id`, `funcionario_id`, `valor`, `desconto`, `comissao`, `data`, `hora`, `observacao`) VALUES ('1', '1', '1', '1', '150.00', '0.00', '10.00', '2026-04-06', '14:27:00', 'teste');
INSERT INTO `agendamentos` (`id`, `cliente_id`, `servico_id`, `funcionario_id`, `valor`, `desconto`, `comissao`, `data`, `hora`, `observacao`) VALUES ('2', '1', '1', '1', '150.00', '0.00', '15.00', '2026-04-02', '14:27:00', '');

DROP TABLE IF EXISTS `financeiro`;
CREATE TABLE `financeiro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) NOT NULL,
  `categoria` varchar(255) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `financeiro` (`id`, `descricao`, `categoria`, `valor`, `tipo`, `data`) VALUES ('1', 'manicure', 'Serviço', '150.00', 'entrada', '2026-04-06');
INSERT INTO `financeiro` (`id`, `descricao`, `categoria`, `valor`, `tipo`, `data`) VALUES ('2', 'pagamento alguel', 'Aluguel', '300.00', 'saida', '2026-04-07');
INSERT INTO `financeiro` (`id`, `descricao`, `categoria`, `valor`, `tipo`, `data`) VALUES ('3', 'pagamento alguel', 'Aluguel', '300.00', 'saida', '2026-04-07');

SET FOREIGN_KEY_CHECKS=1;
