-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19/11/2025 às 01:37
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
-- Banco de dados: `biblioteca_bd`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `autor`
--

CREATE TABLE `autor` (
  `id_autor` int(11) NOT NULL,
  `nome_autor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `autor`
--

INSERT INTO `autor` (`id_autor`, `nome_autor`) VALUES
(1, 'Jonathan Lamim Antunes'),
(2, 'Rosangela Marquesone'),
(3, 'Robert C. Martin'),
(4, 'Charles Wheelan'),
(6, 'Hiroko Nishimura'),
(7, 'Daniel Romero'),
(8, 'Behrouz A. Forouzan'),
(9, 'Caelum'),
(10, 'Vinícius Carvalho'),
(11, 'Steve M. Burnett'),
(12, 'John Z. Sonmez'),
(13, 'Janaina Silva de Souza'),
(14, 'Ellen Siever, Aaron Weber'),
(15, 'Katsuhiko Ogata'),
(16, 'Evaldo Junior Bento'),
(18, 'Eric Evans'),
(19, 'C. J. Date'),
(20, 'David Paniz');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome_categoria` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nome_categoria`) VALUES
(2, 'Arquitetura de Software'),
(3, 'Data Science'),
(4, 'DevOps'),
(5, 'Computação em Nuvem'),
(6, 'Redes de Computadores'),
(7, 'Desenvolvimento Web'),
(8, 'Banco de Dados'),
(9, 'Desenvolvimento Pessoal'),
(10, 'Hardware'),
(11, 'Sistemas Operacionais / Linux'),
(12, 'Engenharia / Automação');

-- --------------------------------------------------------

--
-- Estrutura para tabela `editora`
--

CREATE TABLE `editora` (
  `id_editora` int(11) NOT NULL,
  `nome_editora` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `editora`
--

INSERT INTO `editora` (`id_editora`, `nome_editora`) VALUES
(1, 'Casa do Código'),
(2, 'Alta Books'),
(3, 'Novatec'),
(4, 'Zahar'),
(5, 'Bookman'),
(6, 'O\'Reilly'),
(7, 'Outras');

-- --------------------------------------------------------

--
-- Estrutura para tabela `escritor`
--

CREATE TABLE `escritor` (
  `FK_LIVRO_id_livro` int(11) NOT NULL,
  `FK_AUTOR_id_autor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `escritor`
--

INSERT INTO `escritor` (`FK_LIVRO_id_livro`, `FK_AUTOR_id_autor`) VALUES
(12, 3),
(13, 2),
(14, 1),
(15, 4),
(17, 6),
(18, 7),
(19, 8),
(20, 9),
(21, 10),
(22, 11),
(23, 12),
(24, 13),
(25, 14),
(26, 15),
(28, 16),
(29, 18),
(30, 19),
(31, 20);

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id_favorito` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_livro` int(11) NOT NULL,
  `data_favoritado` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `favoritos`
--

INSERT INTO `favoritos` (`id_favorito`, `id_user`, `id_livro`, `data_favoritado`) VALUES
(11, 2, 31, '2025-11-18 21:27:48');

-- --------------------------------------------------------

--
-- Estrutura para tabela `livro`
--

CREATE TABLE `livro` (
  `id_livro` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `data_publi` date NOT NULL,
  `pdf` varchar(255) NOT NULL,
  `fk_editora` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livro`
--

INSERT INTO `livro` (`id_livro`, `titulo`, `descricao`, `data_publi`, `pdf`, `fk_editora`) VALUES
(12, 'Arquitetura Limpa', 'Em \"Arquitetura Limpa\", Robert C. Martin apresenta uma abordagem universal...', '2019-01-01', '691a0c432a35d.pdf', 2),
(13, 'Big Data', 'Este livro serve como um guia introdutório e prático para o universo do Big Data...', '2016-12-02', '691a0beb5e53a.pdf', 1),
(14, 'Amazon AWS', 'Este livro é um guia \"mão na massa\" voltado para desenvolvedores...', '2016-12-16', '691a0b91dbd89.pdf', 1),
(15, 'Estatística: O que é, para que serve', 'Charles Wheelan \"despe\" a estatística de sua complexidade matemática...', '2016-03-10', '691a0cd6a756e.pdf', 4),
(17, 'AWS for Non-Engineers', 'O guia essencial de Hiroko Nishimura para desmistificar a AWS...', '2022-12-13', '691b760c41bdb.pdf', 7),
(18, 'Containers com Docker', 'Guia prático para quem deseja dominar o Docker...', '2015-01-01', '691b76cc78245.pdf', 1),
(19, 'Comunicação de Dados e Redes', 'Referência clássica e abrangente na área de redes...', '2008-01-01', '691b772acb791.pdf', 5),
(20, 'Desenvolvimento Web com HTML, CSS e JS', 'Material oficial do curso de formação front-end da Caelum...', '2019-06-04', '691b77afaf00e.pdf', 1),
(21, 'PostgreSQL', 'Banco de dados para aplicações web modernas...', '2017-03-01', '691b77fb98e05.pdf', 1),
(22, 'AWS For Beginners', 'Guia direto para os fundamentos da Amazon Web Services...', '2021-01-01', '691b784cab6f2.pdf', 7),
(23, 'Soft Skills', 'Manual foca na outra metade da vida de um desenvolvedor...', '2020-11-11', '691b78cb5ae32.pdf', 7),
(24, 'Montagem e Manutenção de Computadores', 'Guia técnico e didático voltado para a formação...', '2016-11-10', '691b7924c3db9.pdf', 7),
(25, 'Linux: O Guia Essencial', 'Referência indispensável para usuários Linux...', '2006-01-01', '691b798ecbc9d.pdf', 6),
(26, 'Engenharia de Controle Moderno', 'A bíblia da teoria de controle...', '2010-01-01', '691b79e1248a2.pdf', 5),
(28, 'Desenvolvimento web com PHP e MySQL', 'Guia prático para desenvolvimento de aplicações dinâmicas...', '2013-10-01', '691b813dadf11.pdf', 1),
(29, 'Domain-Driven Design: Referência', 'Sumário essencial criado por Eric Evans...', '2015-01-01', '691b8190c51d6.pdf', 2),
(30, 'Introdução a Sistemas de Bancos de Dados', 'Considerada a bíblia acadêmica dos bancos de dados...', '2004-04-14', '691b81f58a940.pdf', 7),
(31, 'NoSQL', 'Como armazenar os dados de uma aplicação moderna...', '2016-01-01', '691b82262f253.pdf', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `temas`
--

CREATE TABLE `temas` (
  `fk_LIVRO_id_livro` int(11) NOT NULL,
  `fk_CATEGORIA_id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `temas`
--

INSERT INTO `temas` (`fk_LIVRO_id_livro`, `fk_CATEGORIA_id_categoria`) VALUES
(12, 2),
(13, 2),
(14, 4),
(15, 3),
(17, 5),
(18, 4),
(19, 6),
(20, 7),
(21, 8),
(22, 5),
(23, 9),
(24, 10),
(25, 11),
(26, 12),
(28, 7),
(29, 2),
(30, 8),
(31, 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_user` int(11) NOT NULL,
  `endereco` int(11) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `tipo` varchar(15) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_user`, `endereco`, `data_nascimento`, `nome`, `email`, `senha`, `cpf`, `tipo`, `is_active`) VALUES
(1, NULL, '2000-12-12', 'alef', 'alef@gmail.com', '$2y$10$zMM9BR/eLytmKtEE4Dq2MeTY7a8wxNvt9GoHqAMJLUPG5FZRHtJbC', '123.123.123-12', 'admin', 1),
(2, NULL, '2000-12-12', 'ana', 'ana@gmail.com', '$2y$10$HzrgcWhPdRV0lMRqYavemuPHc8BbRkIjWVl.cWhx.dLnxIWPDWfPG', '123.321.123-32', 'cliente', 1),
(3, NULL, '2000-12-12', 'vitor', 'vitor@gmail.com', '$2y$10$yZXYKwAUp9uXqSfrqo.jVumsrXR3zHzS7m5wmrRVDte7cjBXVNFte', '123.456.789-10', 'cliente', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`id_autor`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `editora`
--
ALTER TABLE `editora`
  ADD PRIMARY KEY (`id_editora`);

--
-- Índices de tabela `escritor`
--
ALTER TABLE `escritor`
  ADD PRIMARY KEY (`FK_LIVRO_id_livro`,`FK_AUTOR_id_autor`),
  ADD KEY `fk_escritor_autor` (`FK_AUTOR_id_autor`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id_favorito`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_livro` (`id_livro`);

--
-- Índices de tabela `livro`
--
ALTER TABLE `livro`
  ADD PRIMARY KEY (`id_livro`),
  ADD KEY `fk_livro_editora` (`fk_editora`);

--
-- Índices de tabela `temas`
--
ALTER TABLE `temas`
  ADD PRIMARY KEY (`fk_LIVRO_id_livro`,`fk_CATEGORIA_id_categoria`),
  ADD KEY `fk_temas_categoria` (`fk_CATEGORIA_id_categoria`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `autor`
--
ALTER TABLE `autor`
  MODIFY `id_autor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `editora`
--
ALTER TABLE `editora`
  MODIFY `id_editora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id_favorito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `livro`
--
ALTER TABLE `livro`
  MODIFY `id_livro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `escritor`
--
ALTER TABLE `escritor`
  ADD CONSTRAINT `fk_escritor_autor` FOREIGN KEY (`FK_AUTOR_id_autor`) REFERENCES `autor` (`id_autor`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_escritor_livro` FOREIGN KEY (`FK_LIVRO_id_livro`) REFERENCES `livro` (`id_livro`) ON DELETE CASCADE;

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuarios` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`id_livro`) REFERENCES `livro` (`id_livro`) ON DELETE CASCADE;

--
-- Restrições para tabelas `livro`
--
ALTER TABLE `livro`
  ADD CONSTRAINT `fk_livro_editora` FOREIGN KEY (`fk_editora`) REFERENCES `editora` (`id_editora`) ON DELETE SET NULL;

--
-- Restrições para tabelas `temas`
--
ALTER TABLE `temas`
  ADD CONSTRAINT `fk_temas_categoria` FOREIGN KEY (`fk_CATEGORIA_id_categoria`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_temas_livro` FOREIGN KEY (`fk_LIVRO_id_livro`) REFERENCES `livro` (`id_livro`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
