-- Cria o banco de dados (se ele não existir)
CREATE DATABASE IF NOT EXISTS biblioteca_bd;
-- Seleciona o banco para usar
USE biblioteca_bd;

-- -----------------------------------------------------
-- Tabela 'endereco'
-- -----------------------------------------------------
CREATE TABLE endereco (
id_endereco INT PRIMARY KEY AUTO_INCREMENT,
cep VARCHAR (9) NOT NULL,
rua VARCHAR (50) NOT NULL,
numero INT NOT NULL,
complemento VARCHAR (100),
bairro VARCHAR (50) NOT NULL,
cidade VARCHAR (50) NOT NULL,
estado VARCHAR (50) NOT NULL
);

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
is_active TINYINT(1) NOT NULL DEFAULT 1, -- Para desativar (1 = ativo, 0 = inativo)
FOREIGN KEY (endereco) REFERENCES endereco (id_endereco)
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
preco DECIMAL (10, 2) NOT NULL,
data_publi DATE NOT NULL,
categoria INT NOT NULL,
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
-- Tabela 'pedido'
-- -----------------------------------------------------
CREATE TABLE pedido (
id_pedido INT PRIMARY KEY AUTO_INCREMENT,
data_pedido DATETIME NOT NULL,
valor_total DECIMAL (10, 2) NOT NULL,
usuario INT NOT NULL,
FOREIGN KEY (usuario) REFERENCES usuarios (id_user)
-- IMPORTANTE: Se você deletar o usuário com "soft delete",
-- o pedido é mantido. Se você usar 'ON DELETE CASCADE' aqui,
-- o pedido será apagado junto com o usuário.
);

-- -----------------------------------------------------
-- Tabela 'pagamento'
-- -----------------------------------------------------
CREATE TABLE pagamento (
id_pagamento INT PRIMARY KEY AUTO_INCREMENT,
metodo VARCHAR (20) NOT NULL,
status_ VARCHAR (20) NOT NULL,
dt_pagamento DATETIME NOT NULL,
valor_total DECIMAL (10, 2) NOT NULL,
pedido INT NOT NULL,
FOREIGN KEY (pedido) REFERENCES pedido (id_pedido)
-- Se o 'pedido' for deletado, o pagamento (filho) também deve ser.
-- Recomendo adicionar: ON DELETE CASCADE
); 

-- -----------------------------------------------------
-- Tabela 'avaliacao'
-- -----------------------------------------------------
CREATE TABLE avaliacao (
id_avaliacao INT PRIMARY KEY AUTO_INCREMENT,
nota INT NOT NULL,
comentario VARCHAR (200),
dt_avaliacao DATE NOT NULL,
usuario INT NOT NULL,
livro INT NOT NULL,
FOREIGN KEY (usuario) REFERENCES usuarios (id_user),
FOREIGN KEY (livro) REFERENCES livro (id_livro)
-- Mesma coisa aqui: adicione ON DELETE CASCADE se quiser
-- que as avaliações sumam ao deletar o usuário/livro.
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

CREATE TABLE contem (
livro INT NOT NULL,
pedido INT NOT NULL,
PRIMARY KEY (livro, pedido),
FOREIGN KEY (livro) REFERENCES livro (id_livro),
FOREIGN KEY (pedido) REFERENCES pedido (id_pedido)
);