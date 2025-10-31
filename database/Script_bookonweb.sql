CREATE DATABASE thebookisontheweb;
USE thebookisontheweb;

CREATE TABLE endereco (
id_endereco INT PRIMARY KEY NOT NULL,
cep VARCHAR (9) NOT NULL,
rua VARCHAR (50) NOT NULL,
numero INT NOT NULL,
complemento VARCHAR (100),
bairro VARCHAR (50) NOT NULL,
cidade VARCHAR (50) NOT NULL,
estado VARCHAR (50) NOT NULL
);

CREATE TABLE usuarios (
id_user INT PRIMARY KEY NOT NULL,
endereco INT,
nome VARCHAR (50) NOT NULL,
email VARCHAR (50) UNIQUE NOT NULL,
senha VARCHAR (50) NOT NULL,
cpf VARCHAR (14) NOT NULL,
tipo VARCHAR (15),
FOREIGN KEY (endereco) REFERENCES endereco (id_endereco)
); 

CREATE TABLE pedido (
id_pedido INT PRIMARY KEY NOT NULL,
data_pedido DATETIME NOT NULL,
valor_total DECIMAL (10, 2) NOT NULL,
usuario INT NOT NULL,
pagamento INT NOT NULL,
FOREIGN KEY (usuario) REFERENCES usuarios (id_user)
);

CREATE TABLE pagamento (
id_pagamento INT PRIMARY KEY NOT NULL,
metodo VARCHAR (20) NOT NULL,
status_ VARCHAR (20) NOT NULL,
dt_pagamento DATETIME NOT NULL,
valor_total DECIMAL (10, 2) NOT NULL,
pedido INT NOT NULL,
FOREIGN KEY (pedido) REFERENCES pedido (id_pedido)
); 

CREATE TABLE categoria (
id_categoria INT PRIMARY KEY NOT NULL,
nome_categoria VARCHAR (30) NOT NULL
);

CREATE TABLE livro (
id_livro INT PRIMARY KEY NOT NULL,
titulo VARCHAR (50) NOT NULL,
descricao VARCHAR (200) NOT NULL,
preco DECIMAL (10, 2) NOT NULL,
data_publi DATE NOT NULL,
categoria INT NOT NULL,
FOREIGN KEY (categoria) REFERENCES categoria (id_categoria)
);

CREATE  TABLE autor (
id_autor INT PRIMARY KEY NOT NULL,
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
id_avaliacao INT PRIMARY KEY NOT NULL,
nota INT NOT NULL,
comentario VARCHAR (200),
dt_avaliacao DATE NOT NULL,
usuario INT NOT NULL,
livro INT NOT NULL,
FOREIGN KEY (usuario) REFERENCES usuarios (id_user),
FOREIGN KEY (livro) REFERENCES livro (id_livro)
);

