-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/11/2025 às 22:01
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
(4, 'Charles Wheelan, Zahar'),
(6, 'Hiroko Nishimura'),
(7, 'Daniel Romero'),
(8, 'Behrouz A. Forouzan'),
(9, 'Caelum'),
(10, 'Vinícius Carvalho'),
(11, 'Steve M. Burnett'),
(12, 'John Z. Sonmez'),
(13, 'Janaina Silva de Souza'),
(14, 'Ellen Siever, Aaron Weber, Stephen Figgins'),
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
  `nome_categoria` varchar(30) NOT NULL
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
(4, 15),
(6, 17),
(7, 18),
(8, 19),
(9, 20),
(10, 21),
(11, 22),
(12, 23),
(13, 24),
(14, 25),
(15, 26),
(16, 28),
(18, 29),
(19, 30),
(20, 31);

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
(4, 2, 31, '2025-11-17 17:34:17');

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
(15, 'Estatística: O que é, para que serve, como funciona', 'Charles Wheelan \"despe\" a estatística de sua complexidade matemática (daí o título original Naked Statistics) para focar no raciocínio lógico que existe por trás dos números. O livro é famoso por usar exemplos divertidos — como o sistema de recomendação da Netflix, o problema de Monty Hall e fraudes em testes escolares — para explicar conceitos complexos.\r\nA premissa central é que a estatística é uma ferramenta poderosa para \"encontrar a verdade\" em meio a montanhas de dados, mas também é perigosa se usada incorretamente (ou maliciosamente) para distorcer a realidade. É um livro sobre alfabetização de dados.', '2016-03-10', 3, '691a0cd6a756e.pdf'),
(17, 'AWS for Non-Engineers', '\"AWS for Non-Engineers\" é o guia essencial de Hiroko Nishimura para desmistificar a Amazon Web Services (AWS) para profissionais sem background técnico. A obra traduz o complexo jargão da nuvem para uma linguagem de negócios acessível, ideal para gerentes, profissionais de marketing, vendas e finanças.\r\n\r\nFocando no \"o que\" e \"por que\" da tecnologia, em vez de códigos densos, o livro explica os principais serviços da AWS (EC2, S3, RDS), conceitos de segurança e, crucialmente, o gerenciamento de custos e faturamento (FinOps). O leitor aprende a dialogar com confiança com equipes de TI e a tomar decisões estratégicas sobre infraestrutura.\r\n\r\nAlém de ser um recurso valioso para o dia a dia corporativo, o conteúdo serve como preparação robusta para o exame de certificação AWS Certified Cloud Practitioner. É a leitura definitiva para quem precisa entender a nuvem sem precisar programar.', '2022-12-13', 5, '691b760c41bdb.pdf'),
(18, 'Containers com Docker: Do desenvolvimento à produção', 'Este livro é um guia prático para quem deseja dominar o Docker, a ferramenta que revolucionou a forma como desenvolvemos e implantamos software. Daniel Romero aborda desde os conceitos básicos de containers até a orquestração em produção. O leitor aprenderá a criar imagens, gerenciar containers, trabalhar com volumes e redes, e utilizar o Docker Compose para definir aplicações multi-container. É ideal para desenvolvedores e administradores de sistemas que buscam agilidade, portabilidade e eficiência em seus ambientes de desenvolvimento e produção, eliminando o clássico problema de \"na minha máquina funciona\".', '2015-01-01', 4, '691b76cc78245.pdf'),
(19, 'Comunicação de Dados e Redes de Computadores (4ª Ed.)', 'Uma referência clássica e abrangente na área de redes. Behrouz Forouzan apresenta os conceitos complexos de comunicação de dados de forma visual e acessível. O livro cobre o modelo OSI e a arquitetura TCP/IP com profundidade, detalhando cada camada, desde a física até a de aplicação. Tópicos como transmissão de dados, comutação, redes LAN/WAN, segurança de redes e protocolos de internet são explicados com clareza. É uma leitura fundamental para estudantes e profissionais que desejam uma base sólida teórica e prática sobre como as redes modernas conectam o mundo.', '2008-01-01', 6, '691b772acb791.pdf'),
(20, 'Desenvolvimento Web com HTML, CSS e JavaScript (Apostila WD-43)', 'Material oficial do curso de formação front-end da Caelum. Esta obra guia o iniciante através dos pilares da web: HTML para estruturação, CSS para estilização e layout, e JavaScript para interatividade. Com uma abordagem prática \"mão na massa\", o leitor constrói projetos reais enquanto aprende sobre semântica, seletores, design responsivo, manipulação do DOM e boas práticas de mercado. É o ponto de partida ideal para quem deseja ingressar na carreira de desenvolvimento web, oferecendo a base necessária para avançar para frameworks modernos.', '2019-06-04', 7, '691b77afaf00e.pdf'),
(21, 'PostgreSQL: Banco de dados para aplicações web modernas', 'Vinícius Carvalho apresenta o PostgreSQL não apenas como um banco de dados relacional, mas como uma poderosa plataforma de dados para aplicações modernas. O livro vai além do SQL básico, explorando recursos avançados como tipos de dados JSON para armazenar documentos (NoSQL), índices textuais para busca full-text, geoprocessamento com PostGIS e extensões. O leitor aprenderá a modelar, otimizar consultas e administrar o banco, tirando proveito da robustez e versatilidade que tornaram o PostgreSQL um dos bancos de dados open source mais populares do mundo.', '2017-03-01', 8, '691b77fb98e05.pdf'),
(22, 'AWS For Beginners', '\"AWS For Beginners\" é um guia direto para os fundamentos da Amazon Web Services. O livro descomplica a vasta gama de serviços da AWS, focando no que é essencial para iniciantes. Cobre os conceitos centrais de computação em nuvem (IaaS, PaaS, SaaS) e introduz os serviços principais como EC2, S3, RDS e IAM. Com uma linguagem simples, o autor explica como navegar no console da AWS, configurar sua primeira instância e entender o modelo de segurança e custos. É uma leitura rápida para quem precisa de uma visão geral e prática para começar a usar a nuvem da Amazon.', '2021-01-01', 5, '691b784cab6f2.pdf'),
(23, 'Soft Skills: The Software Developer\'s Life Manual', 'Diferente da maioria dos livros técnicos, este manual foca na outra metade da vida de um desenvolvedor: as habilidades comportamentais e de carreira. John Sonmez aborda temas cruciais como gestão de carreira, produtividade, finanças pessoais, fitness e marketing pessoal para programadores. O livro ensina como negociar salários, lidar com chefes e colegas, evitar o burnout e construir uma marca profissional forte. É um guia holístico para desenvolvedores que desejam não apenas escrever bom código, mas também construir uma vida e uma carreira prósperas e equilibradas.', '2020-11-11', 9, '691b78cb5ae32.pdf'),
(24, 'Livro de Montagem e Manutenção de Computadores', 'Um guia técnico e didático voltado para a formação profissional em suporte de TI. O livro detalha a arquitetura de computadores, explicando a função de cada componente (placa-mãe, processador, memória, armazenamento). O leitor é guiado passo a passo pelos procedimentos de montagem segura, configuração de BIOS/UEFI, instalação de sistemas operacionais e diagnóstico de falhas. Com foco na prática, aborda também a manutenção preventiva e corretiva, sendo um recurso valioso para estudantes e técnicos que lidam com o hardware de computadores no dia a dia.', '2016-11-10', 10, '691b7924c3db9.pdf'),
(25, 'Linux: O Guia Essencial (5ª Ed.)', 'Uma referência indispensável para usuários e administradores de sistemas Linux. Este guia condensa a vasta complexidade do sistema operacional em explicações claras e comandos práticos. Cobre desde a estrutura de diretórios e gerenciamento de arquivos até a administração de usuários, redes e shell scripting. Com foco na linha de comando, o livro serve como um manual de consulta rápida para as ferramentas e utilitários essenciais do dia a dia, sendo útil tanto para quem está migrando para o Linux quanto para veteranos que precisam relembrar a sintaxe de comandos específicos.', '2006-01-01', 11, '691b798ecbc9d.pdf'),
(26, 'Engenharia de Controle Moderno (5ª Ed.)', 'A \"bíblia\" da teoria de controle, utilizada mundialmente em cursos de engenharia. Ogata apresenta uma análise rigorosa e abrangente dos sistemas de controle de tempo contínuo. O livro aborda a modelagem matemática de sistemas dinâmicos, análise de resposta transitória e estacionária, e o design de sistemas de controle utilizando o método do lugar das raízes e resposta em frequência. Com forte ênfase no uso do MATLAB para solução de problemas, esta obra é fundamental para engenheiros que projetam sistemas de automação, robótica e controle de processos industriais.', '2010-01-01', 12, '691b79e1248a2.pdf'),
(28, 'Desenvolvimento web com PHP e MySQL', 'Este livro é um guia prático para o desenvolvimento de aplicações web dinâmicas, unindo a linguagem PHP ao banco de dados MySQL. Evaldo Junior Bento conduz o leitor desde os conceitos básicos do protocolo HTTP e a configuração do ambiente até a criação de sistemas robustos.\r\nO conteúdo aborda a criação de formulários, sessões, cookies e a manipulação de banco de dados, culminando no desenvolvimento de um gerenciador de conteúdo completo. É ideal para iniciantes que desejam entender como a web funciona \"por baixo do capô\" e para desenvolvedores que buscam consolidar seus conhecimentos na stack LAMP (Linux, Apache, MySQL, PHP), uma das mais populares do mercado.', '2013-10-01', 7, '691b813dadf11.pdf'),
(29, 'Domain-Driven Design: Referência', '\"DDD Referência\" é um sumário essencial criado por Eric Evans para servir como um guia rápido aos conceitos apresentados em sua obra seminal \"Domain-Driven Design\". Este livro condensa as definições cruciais e os padrões de projeto que formam a base do desenvolvimento orientado ao domínio.\r\nEle serve como um mapa para a linguagem onipresente, contextos delimitados (Bounded Contexts), entidades, agregados e objetos de valor. É uma ferramenta de consulta indispensável para arquitetos e desenvolvedores que já praticam ou estão estudando DDD e precisam de acesso rápido às definições canônicas para facilitar a comunicação e a modelagem de sistemas complexos.', '2015-01-01', 2, '691b8190c51d6.pdf'),
(30, 'Introdução a Sistemas de Bancos de Dados (8ª Ed.)', 'Considerada a \"bíblia\" acadêmica dos bancos de dados, esta obra de C.J. Date oferece a introdução mais completa e rigorosa sobre a teoria de sistemas de banco de dados. O livro aprofunda-se no modelo relacional, álgebra relacional, normalização e integridade de dados, estabelecendo os fundamentos teóricos que sustentam a tecnologia SQL moderna.\r\nAlém da teoria clássica, a 8ª edição aborda temas avançados como otimização de consultas, concorrência, recuperação de falhas e a integração com objetos e XML. É leitura obrigatória para estudantes de ciência da computação e profissionais que desejam dominar a ciência por trás do armazenamento e recuperação de dados.', '2004-04-14', 8, '691b81f58a940.pdf'),
(31, 'NoSQL: Como armazenar os dados de uma aplicação moderna', 'David Paniz desmistifica o movimento NoSQL, apresentando alternativas aos bancos relacionais tradicionais para lidar com os desafios de escalabilidade e flexibilidade das aplicações modernas. O livro explora os quatro principais tipos de bancos NoSQL: chave-valor (Redis), documentos (MongoDB), colunar (Cassandra) e grafos (Neo4j).\r\nAtravés de exemplos práticos, o leitor aprenderá quando e como utilizar cada tecnologia, entendendo seus pontos fortes e fracos em comparação ao modelo relacional. É um guia fundamental para arquitetos e desenvolvedores que precisam tomar decisões informadas sobre persistência de dados em cenários de alta performance e Big Data.', '2016-01-01', 8, '691b82262f253.pdf');

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
  MODIFY `id_autor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id_favorito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
