-- Cria o banco de dados (se ele não existir)
CREATE DATABASE IF NOT EXISTS biblioteca_bd;
-- Seleciona o banco para usar
USE biblioteca_bd;

-- -----------------------------------------------------
-- Tabela 'usuarios'
-- (Com 'is_active' para o soft delete)
-- -----------------------------------------------------
CREATE TABLE usuarios (
id_user INT PRIMARY KEY AUTO_INCREMENT,
endereco INT NULL,
data_nascimento DATE NULL,
nome VARCHAR (50) NOT NULL,
email VARCHAR (50) UNIQUE NOT NULL,
senha VARCHAR (255) NOT NULL, 
cpf VARCHAR (14) NOT NULL,
tipo VARCHAR (15),
is_active TINYINT(1) NOT NULL DEFAULT 1 -- Para desativar (1 = ativo, 0 = inativo)
); 

-- -----------------------------------------------------
-- Tabela 'categoria'
-- -----------------------------------------------------
CREATE TABLE categoria (
id_categoria INT PRIMARY KEY AUTO_INCREMENT,
nome_categoria VARCHAR (30) NOT NULL
);

-- -----------------------------------------------------
-- Tabela 'livro'
-- -----------------------------------------------------
CREATE TABLE livro (
id_livro INT PRIMARY KEY AUTO_INCREMENT,
titulo VARCHAR (50) NOT NULL,
descricao VARCHAR (200) NOT NULL,
data_publi DATE NOT NULL,
categoria INT NOT NULL,
pdf VARCHAR(255) NOT NULL,
FOREIGN KEY (categoria) REFERENCES categoria (id_categoria)
);

-- -----------------------------------------------------
-- Tabela 'autor'
-- -----------------------------------------------------
CREATE TABLE autor (
id_autor INT PRIMARY KEY AUTO_INCREMENT,
nome_autor VARCHAR (50) NOT NULL
);


-- -----------------------------------------------------
-- Tabelas de Ligação (Muitos-para-Muitos)
-- -----------------------------------------------------
CREATE TABLE escritor (
autor INT NOT NULL,
livro INT NOT NULL,
PRIMARY KEY (autor, livro),
FOREIGN KEY (autor) REFERENCES autor (id_autor),
FOREIGN KEY (livro) REFERENCES livro (id_livro)
);


CREATE TABLE IF NOT EXISTS favoritos (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_livro INT NOT NULL,
    data_favoritado DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES usuarios(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_livro) REFERENCES livro(id_livro) ON DELETE CASCADE
);