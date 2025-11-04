CREATE DATABASE bookonweb;
USE bookonweb;

CREATE TABLE endereco (
id_endereco INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
cep VARCHAR (9) NOT NULL,
rua VARCHAR (75) NOT NULL,
numero INT NOT NULL,
complemento VARCHAR (100),
bairro VARCHAR (75) NOT NULL,
cidade VARCHAR (75) NOT NULL,
estado VARCHAR (75) NOT NULL
);

CREATE TABLE usuarios (
id_user INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
endereco INT,
nome VARCHAR (50) NOT NULL,
email VARCHAR (50) UNIQUE NOT NULL,
senha VARCHAR (50) NOT NULL,
cpf VARCHAR (14) NOT NULL,
tipo VARCHAR (15),
FOREIGN KEY (endereco) REFERENCES endereco (id_endereco)
); 

CREATE TABLE pedido (
id_pedido INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
data_pedido DATETIME NOT NULL,
valor_total DECIMAL (10, 2) NOT NULL,
usuario INT NOT NULL,
pagamento INT NOT NULL,
FOREIGN KEY (usuario) REFERENCES usuarios (id_user)
);

CREATE TABLE pagamento (
id_pagamento INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
metodo VARCHAR (20) NOT NULL,
status_ VARCHAR (20) NOT NULL,
dt_pagamento DATETIME NOT NULL,
valor_total DECIMAL (10, 2) NOT NULL,
pedido INT NOT NULL,
FOREIGN KEY (pedido) REFERENCES pedido (id_pedido)
); 

CREATE TABLE categoria (
id_categoria INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
nome_categoria VARCHAR (75) NOT NULL
);

CREATE TABLE livro (
id_livro INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
pdf VARCHAR(255),
titulo VARCHAR (100) NOT NULL,
descricao VARCHAR (200),
preco DECIMAL (10, 2) NOT NULL,
data_publi DATE,
categoria INT NOT NULL,
FOREIGN KEY (categoria) REFERENCES categoria (id_categoria)
);

CREATE  TABLE autor (
id_autor INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
nome_autor VARCHAR (50) NOT NULL
);

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

CREATE TABLE avaliacao (
id_avaliacao INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
nota INT NOT NULL,
comentario VARCHAR (200),
dt_avaliacao DATE NOT NULL,
usuario INT NOT NULL,
livro INT NOT NULL,
FOREIGN KEY (usuario) REFERENCES usuarios (id_user),
FOREIGN KEY (livro) REFERENCES livro (id_livro)
);

INSERT INTO usuarios (nome, email, senha, cpf, tipo) VALUES
('Admin User', 'admin@bookstore.com', 'admin123', '123.456.789-00', 'admin'),
('Regular User', 'user@bookstore.com', 'user123', '987.654.321-00', 'user');