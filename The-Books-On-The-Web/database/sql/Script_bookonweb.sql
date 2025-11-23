CREATE DATABASE biblioteca_bd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_bd;

-- ----------------------------------------------------------
-- 1. TABELAS AUXILIARES (Sem dependências)
-- ----------------------------------------------------------

CREATE TABLE usuarios (
  id_user int(11) PRIMARY KEY AUTO_INCREMENT,
  nome varchar(50) NOT NULL,
  email varchar(50) UNIQUE NOT NULL,
  senha varchar(255) NOT NULL,
  cpf varchar(14) NOT NULL,
  data_nascimento date DEFAULT NULL,
  tipo varchar(15) DEFAULT 'cliente',
  is_active tinyint(1) NOT NULL DEFAULT 1
)

CREATE TABLE autor (
  id_autor int(11) PRIMARY KEY AUTO_INCREMENT,
  nome_autor varchar(100) NOT NULL
)

CREATE TABLE categoria (
  id_categoria int(11) PRIMARY KEY AUTO_INCREMENT,
  nome_categoria varchar(50) NOT NULL
)

CREATE TABLE editora (
  id_editora int(11) PRIMARY KEY AUTO_INCREMENT,
  nome_editora varchar(50) NOT NULL
)

-- ----------------------------------------------------------
-- 2. TABELA PRINCIPAL (LIVRO)
-- ----------------------------------------------------------

CREATE TABLE livro (
  id_livro int(11) PRIMARY KEY AUTO_INCREMENT,
  titulo varchar(255) NOT NULL,
  descricao text NOT NULL,
  data_publi date NOT NULL,
  pdf varchar(255) NOT NULL,
  id_editora int(11) DEFAULT NULL,
  CONSTRAINT fk_livro_editora FOREIGN KEY (id_editora) REFERENCES editora (id_editora) ON DELETE SET NULL
)

-- ----------------------------------------------------------
-- 3. TABELAS DE LIGAÇÃO (Muitos-para-Muitos)
-- ----------------------------------------------------------

-- Liga Livro <-> Autor
CREATE TABLE escritor (
  id_livro int(11) NOT NULL,
  id_autor int(11) NOT NULL,
  PRIMARY KEY (id_livro, id_autor),
  CONSTRAINT fk_escritor_livro FOREIGN KEY (id_livro) REFERENCES livro (id_livro) ON DELETE CASCADE,
  CONSTRAINT fk_escritor_autor FOREIGN KEY (id_autor) REFERENCES autor (id_autor) ON DELETE CASCADE
)

-- Liga Livro <-> Categoria
CREATE TABLE temas (
  id_livro int(11) NOT NULL,
  id_categoria int(11) NOT NULL,
  PRIMARY KEY (id_livro, id_categoria),
  CONSTRAINT fk_temas_livro FOREIGN KEY (id_livro) REFERENCES livro (id_livro) ON DELETE CASCADE,
  CONSTRAINT fk_temas_categoria FOREIGN KEY (id_categoria) REFERENCES categoria (id_categoria) ON DELETE CASCADE
)

-- ----------------------------------------------------------
-- 4. TABELA DE FAVORITOS (Usuário <-> Livro)
-- ----------------------------------------------------------

CREATE TABLE favoritos (
  id_favorito int(11) PRIMARY KEY AUTO_INCREMENT,
  id_user int(11) NOT NULL,
  id_livro int(11) NOT NULL,
  data_favoritado datetime DEFAULT current_timestamp(),
  CONSTRAINT favoritos_user FOREIGN KEY (id_user) REFERENCES usuarios (id_user) ON DELETE CASCADE,
  CONSTRAINT favoritos_livro FOREIGN KEY (id_livro) REFERENCES livro (id_livro) ON DELETE CASCADE
)