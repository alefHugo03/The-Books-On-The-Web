-- Cria o banco de dados (se ele não existir)
CREATE DATABASE IF NOT EXISTS biblioteca_bd;
-- Seleciona o banco para usar
USE biblioteca_bd;

-- ==========================================================
-- CONSTRUÇÃO DAS TABELAS (DDL)
-- ==========================================================

-- 1. TABELAS AUXILIARES (Sem dependências)
-- ----------------------------------------------------------

CREATE TABLE `usuarios` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `tipo` varchar(15) DEFAULT 'cliente',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `autor` (
  `id_autor` int(11) NOT NULL AUTO_INCREMENT,
  `nome_autor` varchar(100) NOT NULL,
  PRIMARY KEY (`id_autor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome_categoria` varchar(50) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `editora` (
  `id_editora` int(11) NOT NULL AUTO_INCREMENT,
  `nome_editora` varchar(50) NOT NULL,
  PRIMARY KEY (`id_editora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2. TABELA PRINCIPAL (LIVRO)
-- ----------------------------------------------------------
-- Depende da tabela 'editora' (1:N)

CREATE TABLE `livro` (
  `id_livro` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `data_publi` date NOT NULL,
  `pdf` varchar(255) NOT NULL,
  `fk_editora` int(11) DEFAULT NULL, -- Chave Estrangeira para Editora
  PRIMARY KEY (`id_livro`),
  KEY `fk_livro_editora` (`fk_editora`),
  CONSTRAINT `fk_livro_editora` FOREIGN KEY (`fk_editora`) REFERENCES `editora` (`id_editora`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 3. TABELAS DE LIGAÇÃO (N:N)
-- ----------------------------------------------------------
-- Dependem de 'livro' e 'autor'/'categoria'

-- Liga Livro <-> Autor (Muitos Autores)
CREATE TABLE `ESCRITOR` (
  `FK_LIVRO_id_livro` int(11) NOT NULL,
  `FK_AUTOR_id_autor` int(11) NOT NULL,
  PRIMARY KEY (`FK_LIVRO_id_livro`, `FK_AUTOR_id_autor`),
  CONSTRAINT `fk_escritor_livro` FOREIGN KEY (`FK_LIVRO_id_livro`) REFERENCES `livro` (`id_livro`) ON DELETE CASCADE,
  CONSTRAINT `fk_escritor_autor` FOREIGN KEY (`FK_AUTOR_id_autor`) REFERENCES `autor` (`id_autor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Liga Livro <-> Categoria (Muitas Categorias)
CREATE TABLE `Temas` (
  `fk_LIVRO_id_livro` int(11) NOT NULL,
  `fk_CATEGORIA_id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`fk_LIVRO_id_livro`, `fk_CATEGORIA_id_categoria`),
  CONSTRAINT `fk_temas_livro` FOREIGN KEY (`fk_LIVRO_id_livro`) REFERENCES `livro` (`id_livro`) ON DELETE CASCADE,
  CONSTRAINT `fk_temas_categoria` FOREIGN KEY (`fk_CATEGORIA_id_categoria`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 4. TABELA DE FAVORITOS (Usuário <-> Livro)
-- ----------------------------------------------------------

CREATE TABLE `favoritos` (
  `id_favorito` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_livro` int(11) NOT NULL,
  `data_favoritado` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_favorito`),
  KEY `id_user` (`id_user`),
  KEY `id_livro` (`id_livro`),
  CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `usuarios` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`id_livro`) REFERENCES `livro` (`id_livro`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;