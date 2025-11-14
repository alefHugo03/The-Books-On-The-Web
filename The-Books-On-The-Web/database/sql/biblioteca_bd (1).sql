-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/11/2025 às 23:17
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
  `nome_autor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacao`
--

CREATE TABLE `avaliacao` (
  `id_avaliacao` int(11) NOT NULL,
  `nota` int(11) NOT NULL,
  `comentario` varchar(200) DEFAULT NULL,
  `dt_avaliacao` date NOT NULL,
  `usuario` int(11) NOT NULL,
  `livro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nome_categoria` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nome_categoria`) VALUES
(2, 'Arquitetura de Software'),
(3, 'Data Science'),
(4, 'DevOps');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contem`
--

CREATE TABLE `contem` (
  `livro` int(11) NOT NULL,
  `pedido` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `escritor`
--

CREATE TABLE `escritor` (
  `autor` int(11) NOT NULL,
  `livro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `livro`
--

CREATE TABLE `livro` (
  `id_livro` int(11) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `data_publi` date NOT NULL,
  `categoria` int(11) NOT NULL,
  `pdf` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livro`
--

INSERT INTO `livro` (`id_livro`, `titulo`, `descricao`, `data_publi`, `categoria`, `pdf`) VALUES
(12, 'Arquitetura Limpa - O Guia do Artesão para Estrutu', 'As regras universais de arquitetura de software aumentam dramaticamente a produtividade dos desenvolvedores ao longo da vida dos sistemas de software. Agora, aproveitando o sucesso dos seus best-selle', '2019-01-01', 2, '6917a15a099f6.pdf'),
(13, 'Big Data - Técnicas e tecnologias para extração de', 'Estamos na era dos dados. Não importa qual seja a sua área de atuação, uma palavra atualmente em evidência é: Big Data. Podemos encontrar casos de uso em que esse conceito permitiu a redução do número', '2016-12-02', 2, '6917a289229c4.pdf'),
(14, 'Amazon AWS - Descomplicando a computação na nuvem', 'Quando se trata de grandes aplicações, infraestrutura é um ponto muito importante, pois é preciso pensar em escalabilidade, gerenciamento e, principalmente, os serviços necessários para seu bom funcio', '2016-12-16', 4, '6917a77549fab.pdf'),
(15, 'Estatistica - O que e, para que serve, como funcio', 'Um livro que nos faz entender os números por trás dos fatos e apreciar a força extraordinária dos dados em diversos aspectos do cotidiano\r\n\r\nA estatística é uma ciência que está em toda parte, muito e', '2016-03-10', 3, '6917a81131c3e.pdf');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido`
--

CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL,
  `data_pedido` datetime NOT NULL,
  `usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, NULL, '2000-12-12', 'ana', 'ana@gmail.com', '$2y$10$HzrgcWhPdRV0lMRqYavemuPHc8BbRkIjWVl.cWhx.dLnxIWPDWfPG', '123.321.123-32', 'cliente', 0),
(3, NULL, '2000-12-12', 'vitor', 'vitor@gmail.com', '$2y$10$yZXYKwAUp9uXqSfrqo.jVumsrXR3zHzS7m5wmrRVDte7cjBXVNFte', '123.456.789-10', 'cliente', 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`id_autor`);

--
-- Índices de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD PRIMARY KEY (`id_avaliacao`),
  ADD KEY `usuario` (`usuario`),
  ADD KEY `livro` (`livro`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `contem`
--
ALTER TABLE `contem`
  ADD PRIMARY KEY (`livro`,`pedido`),
  ADD KEY `pedido` (`pedido`);

--
-- Índices de tabela `escritor`
--
ALTER TABLE `escritor`
  ADD PRIMARY KEY (`autor`,`livro`),
  ADD KEY `livro` (`livro`);

--
-- Índices de tabela `livro`
--
ALTER TABLE `livro`
  ADD PRIMARY KEY (`id_livro`),
  ADD KEY `categoria` (`categoria`);

--
-- Índices de tabela `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `usuario` (`usuario`);

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
  MODIFY `id_autor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `avaliacao`
--
ALTER TABLE `avaliacao`
  MODIFY `id_avaliacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `livro`
--
ALTER TABLE `livro`
  MODIFY `id_livro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD CONSTRAINT `avaliacao_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id_user`),
  ADD CONSTRAINT `avaliacao_ibfk_2` FOREIGN KEY (`livro`) REFERENCES `livro` (`id_livro`);

--
-- Restrições para tabelas `contem`
--
ALTER TABLE `contem`
  ADD CONSTRAINT `contem_ibfk_1` FOREIGN KEY (`livro`) REFERENCES `livro` (`id_livro`),
  ADD CONSTRAINT `contem_ibfk_2` FOREIGN KEY (`pedido`) REFERENCES `pedido` (`id_pedido`);

--
-- Restrições para tabelas `escritor`
--
ALTER TABLE `escritor`
  ADD CONSTRAINT `escritor_ibfk_1` FOREIGN KEY (`autor`) REFERENCES `autor` (`id_autor`),
  ADD CONSTRAINT `escritor_ibfk_2` FOREIGN KEY (`livro`) REFERENCES `livro` (`id_livro`);

--
-- Restrições para tabelas `livro`
--
ALTER TABLE `livro`
  ADD CONSTRAINT `livro_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categoria` (`id_categoria`);

--
-- Restrições para tabelas `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
