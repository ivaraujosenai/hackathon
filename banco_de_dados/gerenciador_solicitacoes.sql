-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/11/2025 às 09:03
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gerenciador_solicitacoes`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cargos`
--

CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL,
  `nome_cargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cargos`
--

INSERT INTO `cargos` (`id_cargo`, `nome_cargo`) VALUES
(1, 'Professor'),
(2, 'Funcionário'),
(3, 'Técnico Administrativo'),
(4, 'Coordenador');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nome_categoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome_categoria`) VALUES
(1, 'Computador'),
(2, 'Impressora'),
(3, 'Rede/Internet'),
(4, 'Software'),
(5, 'Elétrica'),
(6, 'Hidráulica'),
(7, 'Mobiliário');

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacoes`
--

CREATE TABLE `movimentacoes` (
  `id_movimentacao` int(11) NOT NULL,
  `solicitacao_id` int(11) NOT NULL,
  `usuario_admin_id` int(11) DEFAULT NULL,
  `data_movimentacao` datetime NOT NULL DEFAULT current_timestamp(),
  `novo_status` enum('Aberta','Em Andamento','Concluída','Cancelada') DEFAULT NULL,
  `resposta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movimentacoes`
--

INSERT INTO `movimentacoes` (`id_movimentacao`, `solicitacao_id`, `usuario_admin_id`, `data_movimentacao`, `novo_status`, `resposta`) VALUES
(1, 1, 1, '2025-11-14 02:41:32', 'Concluída', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `setores`
--

CREATE TABLE `setores` (
  `id_setor` int(11) NOT NULL,
  `nome_setor` varchar(120) NOT NULL,
  `email_setor` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `setores`
--

INSERT INTO `setores` (`id_setor`, `nome_setor`, `email_setor`) VALUES
(1, 'TI', 'ti@exemplo.com'),
(2, 'Manutenção', 'manutencao@exemplo.com'),
(3, 'Secretaria', 'secretaria@exemplo.com'),
(4, 'Estrutural', 'estrutural@exemplo.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes`
--

CREATE TABLE `solicitacoes` (
  `id_solicitacao` int(11) NOT NULL,
  `nome_solicitante` varchar(120) NOT NULL,
  `matricula_solicitante` varchar(45) NOT NULL,
  `cargo_solicitante` varchar(100) NOT NULL,
  `email_solicitante` varchar(255) NOT NULL,
  `local_problema` varchar(120) NOT NULL,
  `descricao` text NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `prioridade` enum('Baixa','Média','Urgente') NOT NULL,
  `path_imagem` varchar(255) DEFAULT NULL,
  `data_abertura` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Aberta','Em Andamento','Concluída','Cancelada') NOT NULL DEFAULT 'Aberta',
  `setor_responsavel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `solicitacoes`
--

INSERT INTO `solicitacoes` (`id_solicitacao`, `nome_solicitante`, `matricula_solicitante`, `cargo_solicitante`, `email_solicitante`, `local_problema`, `descricao`, `categoria_id`, `prioridade`, `path_imagem`, `data_abertura`, `status`, `setor_responsavel_id`) VALUES
(1, 'renan', '111111', 'Professor', 'renan.a.santos@ba.estudante.senai.br', 'lab. dev', 'instalar o Xampp em dois computadores.', 4, 'Média', 'public/uploads/6916c01d572ce_download.jpg', '2025-11-14 02:37:33', 'Concluída', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios_admin`
--

CREATE TABLE `usuarios_admin` (
  `id_usuario` int(11) NOT NULL,
  `nome_usuario` varchar(120) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `email_usuario` varchar(120) NOT NULL,
  `setor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios_admin`
--

INSERT INTO `usuarios_admin` (`id_usuario`, `nome_usuario`, `senha_hash`, `email_usuario`, `setor_id`) VALUES
(1, 'Admin TI', '$2y$12$KF2jF7Vr4rPg7KvW3Y.knuxsuHNPGCtS9DlQATkWqKs8fmd4nM05q', 'ti@exemplo.com', 1),
(2, 'Admin Manutenção', '$2y$12$KF2jF7Vr4rPg7KvW3Y.knuxsuHNPGCtS9DlQATkWqKs8fmd4nM05q', 'manutencao@exemplo.com', 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  ADD PRIMARY KEY (`id_movimentacao`),
  ADD KEY `solicitacao_id` (`solicitacao_id`),
  ADD KEY `usuario_admin_id` (`usuario_admin_id`);

--
-- Índices de tabela `setores`
--
ALTER TABLE `setores`
  ADD PRIMARY KEY (`id_setor`);

--
-- Índices de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD PRIMARY KEY (`id_solicitacao`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `setor_responsavel_id` (`setor_responsavel_id`);

--
-- Índices de tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email_usuario` (`email_usuario`),
  ADD KEY `setor_id` (`setor_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
  MODIFY `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `setores`
--
ALTER TABLE `setores`
  MODIFY `id_setor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `solicitacoes`
--
ALTER TABLE `solicitacoes`
  MODIFY `id_solicitacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `movimentacoes`
--
ALTER TABLE `movimentacoes`
  ADD CONSTRAINT `fk_movimentacoes_solicitacao` FOREIGN KEY (`solicitacao_id`) REFERENCES `solicitacoes` (`id_solicitacao`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_movimentacoes_usuario` FOREIGN KEY (`usuario_admin_id`) REFERENCES `usuarios_admin` (`id_usuario`) ON DELETE SET NULL;

--
-- Restrições para tabelas `solicitacoes`
--
ALTER TABLE `solicitacoes`
  ADD CONSTRAINT `fk_solicitacoes_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `fk_solicitacoes_setor` FOREIGN KEY (`setor_responsavel_id`) REFERENCES `setores` (`id_setor`);

--
-- Restrições para tabelas `usuarios_admin`
--
ALTER TABLE `usuarios_admin`
  ADD CONSTRAINT `fk_usuarios_setor` FOREIGN KEY (`setor_id`) REFERENCES `setores` (`id_setor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
