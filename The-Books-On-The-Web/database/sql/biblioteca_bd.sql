-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 16/11/2025 às 19:15
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

--
-- Despejando dados para a tabela `autor`
--

INSERT INTO `autor` (`id_autor`, `nome_autor`) VALUES
(1, 'Jonathan Lamim Antunes, Casa do Código'),
(2, 'Rosangela Marquesone, Casa do Código'),
(3, 'Robert C. Martin'),
(4, 'Charles Wheelan, Zahar');

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
-- Estrutura para tabela `escritor`
--

CREATE TABLE `escritor` (
  `autor` int(11) NOT NULL,
  `livro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `escritor`
--

INSERT INTO `escritor` (`autor`, `livro`) VALUES
(1, 14),
(2, 13),
(3, 12),
(4, 15);

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

-- --------------------------------------------------------

--
-- Estrutura para tabela `livro`
--

CREATE TABLE `livro` (
  `id_livro` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` varchar(1000) NOT NULL,
  `data_publi` date NOT NULL,
  `categoria` int(11) NOT NULL,
  `pdf` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `livro`
--

INSERT INTO `livro` (`id_livro`, `titulo`, `descricao`, `data_publi`, `categoria`, `pdf`) VALUES
(12, 'Arquitetura Limpa: O Guia do Artesão para Estrutura e Design de Software', 'Em \"Arquitetura Limpa\", Robert C. Martin apresenta uma abordagem universal para criar sistemas de software que sejam robustos, manuteníveis e escaláveis. O livro defende que as regras da arquitetura de software são as mesmas, independentemente de ser um sistema web, desktop ou mobile.\r\nO foco principal não é sobre escrever código \"bonito\", mas sim sobre gerenciar dependências e criar barreiras (limites) que protejam a lógica de negócios (o coração do software) das ferramentas externas (bancos de dados, frameworks e interfaces de usuário).', '2019-01-01', 2, '691a0c432a35d.pdf'),
(13, 'Big Data - Técnicas e tecnologias para extração de valor dos dados', 'Este livro serve como um guia introdutório e prático para o universo do Big Data. A autora, Rosangela Marquesone, desmistifica o termo que muitas vezes é utilizado apenas como uma \"buzzword\". A obra guia o leitor desde os conceitos fundamentais até à implementação prática, cobrindo todo o ciclo de vida dos dados: captura, armazenamento, processamento, análise e visualização.\r\nÉ uma leitura essencial para quem deseja entender não só o que é Big Data, mas como as empresas estão a utilizar estas tecnologias (como Hadoop e NoSQL) para transformar volumes massivos de dados em decisões de negócio inteligentes.', '2016-12-02', 2, '691a0beb5e53a.pdf'),
(14, 'Amazon AWS - Descomplicando a computação na nuvem', 'Este livro é um guia \"mão na massa\" voltado para desenvolvedores e administradores de sistemas que desejam migrar da hospedagem tradicional (servidores físicos ou VPS simples) para a nuvem da Amazon.\r\nAo contrário de manuais que tentam cobrir todos os centenas de serviços da AWS, Jonathan Lamim foca no \"cinto de utilidades\" essencial: o que você realmente precisa para colocar uma aplicação web robusta no ar. O livro guia o leitor desde a criação da conta gratuita (Free Tier) até a configuração de servidores Linux, bancos de dados e armazenamento de arquivos.', '2016-12-16', 4, '691a0b91dbd89.pdf'),
(15, 'Estatística: O que é, para que serve, como funciona', 'Charles Wheelan \"despe\" a estatística de sua complexidade matemática (daí o título original Naked Statistics) para focar no raciocínio lógico que existe por trás dos números. O livro é famoso por usar exemplos divertidos — como o sistema de recomendação da Netflix, o problema de Monty Hall e fraudes em testes escolares — para explicar conceitos complexos.\r\nA premissa central é que a estatística é uma ferramenta poderosa para \"encontrar a verdade\" em meio a montanhas de dados, mas também é perigosa se usada incorretamente (ou maliciosamente) para distorcer a realidade. É um livro sobre alfabetização de dados.', '2016-03-10', 3, '691a0cd6a756e.pdf');

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
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `escritor`
--
ALTER TABLE `escritor`
  ADD PRIMARY KEY (`autor`,`livro`),
  ADD KEY `livro` (`livro`);

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
  ADD KEY `categoria` (`categoria`);

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
  MODIFY `id_autor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id_favorito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `livro`
--
ALTER TABLE `livro`
  MODIFY `id_livro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  ADD CONSTRAINT `escritor_ibfk_1` FOREIGN KEY (`autor`) REFERENCES `autor` (`id_autor`),
  ADD CONSTRAINT `escritor_ibfk_2` FOREIGN KEY (`livro`) REFERENCES `livro` (`id_livro`);

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
  ADD CONSTRAINT `livro_ibfk_1` FOREIGN KEY (`categoria`) REFERENCES `categoria` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
