-- Cria o banco de dados
CREATE DATABASE IF NOT EXISTS biblioteca_bd;
USE biblioteca_bd;

-- -----------------------------------------------------
-- Tabelas CREATE
-- -----------------------------------------------------

CREATE TABLE endereco (
id_endereco INT PRIMARY KEY AUTO_INCREMENT, -- NOT NULL removido
cep VARCHAR (9) NOT NULL,
rua VARCHAR (50) NOT NULL,
numero INT NOT NULL,
complemento VARCHAR (100),
bairro VARCHAR (50) NOT NULL,
cidade VARCHAR (50) NOT NULL,
estado VARCHAR (50) NOT NULL
);

CREATE TABLE usuarios (
id_user INT PRIMARY KEY AUTO_INCREMENT, -- NOT NULL removido
endereco INT NULL,
data_nascimento DATE NULL,
nome VARCHAR (50) NOT NULL,
email VARCHAR (50) UNIQUE NOT NULL,
senha VARCHAR (255) NOT NULL, 
cpf VARCHAR (14) NOT NULL,
tipo VARCHAR (15),
FOREIGN KEY (endereco) REFERENCES endereco (id_endereco)
); 

CREATE TABLE pedido (
id_pedido INT PRIMARY KEY AUTO_INCREMENT, -- NOT NULL removido
data_pedido DATETIME NOT NULL,
valor_total DECIMAL (10, 2) NOT NULL,
usuario INT NOT NULL,
FOREIGN KEY (usuario) REFERENCES usuarios (id_user)
);

CREATE TABLE pagamento (
id_pagamento INT PRIMARY KEY AUTO_INCREMENT, -- NOT NULL removido
metodo VARCHAR (20) NOT NULL,
status_ VARCHAR (20) NOT NULL,
dt_pagamento DATETIME NOT NULL,
valor_total DECIMAL (10, 2) NOT NULL,
pedido INT NOT NULL,
FOREIGN KEY (pedido) REFERENCES pedido (id_pedido)
); 

CREATE TABLE categoria (
id_categoria INT PRIMARY KEY AUTO_INCREMENT, -- NOT NULL removido
nome_categoria VARCHAR (30) NOT NULL
);

CREATE TABLE livro (
id_livro INT PRIMARY KEY AUTO_INCREMENT, -- NOT NULL removido
titulo VARCHAR (50) NOT NULL,
descricao VARCHAR (200) NOT NULL,
preco DECIMAL (10, 2) NOT NULL,
data_publi DATE NOT NULL,
categoria INT NOT NULL,
FOREIGN KEY (categoria) REFERENCES categoria (id_categoria)
);

CREATE TABLE autor (
id_autor INT PRIMARY KEY AUTO_INCREMENT, -- NOT NULL removido
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
id_avaliacao INT PRIMARY KEY AUTO_INCREMENT, -- NOT NULL removido
nota INT NOT NULL,
comentario VARCHAR (200),
dt_avaliacao DATE NOT NULL,
usuario INT NOT NULL,
livro INT NOT NULL,
FOREIGN KEY (usuario) REFERENCES usuarios (id_user),
FOREIGN KEY (livro) REFERENCES livro (id_livro)
);

-- -----------------------------------------------------
-- INSERTS (Estes permanecem iguais)
-- -----------------------------------------------------

-- 1. Tabelas Principais (Sem dependências)
INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, estado)
VALUES
('08773-120', 'Rua das Flores', 123, 'Apto 4B', 'Vila Jardim', 'Mogi das Cruzes', 'SP'),
('01311-000', 'Av. Paulista', 1500, 'Sala 301', 'Bela Vista', 'São Paulo', 'SP');

INSERT INTO categoria (nome_categoria)
VALUES
('Ficção Científica'),
('Fantasia'),
('Técnico');

INSERT INTO autor (nome_autor)
VALUES
('Frank Herbert'),
('J.R.R. Tolkien'),
('Alef Hugo');

-- 2. Tabelas Dependentes (Nível 1)
INSERT INTO usuarios (endereco, data_nascimento, nome, email, senha, cpf, tipo)
VALUES
(1, '1990-05-15', 'João Silva', 'maria@gmail.com', '73656e6861313233', '111.222.333-44', 'cliente'),
(2, '1985-10-20', 'Maria Souza', 'alef@gmail.com', 'd25230d6d94cf861be33a5e922fca98a', '555.666.777-88', 'admin');

INSERT INTO livro (titulo, descricao, preco, data_publi, categoria)
VALUES
('Duna', 'Um épico de ficção científica...', 45.50, '1965-08-01', 1),
('O Senhor dos Anéis', 'A jornada de Frodo para destruir o Anel...', 89.90, '1954-07-29', 2),
('Manual de MySQL', 'Aprenda SQL com exemplos.', 120.00, '2025-01-10', 3);

-- 3. Tabelas de Ligação (Muitos-para-Muitos)
INSERT INTO escritor (autor, livro)
VALUES
(1, 1), 
(2, 2), 
(3, 3); 

-- 4. Pedidos e Pagamentos
INSERT INTO pedido (data_pedido, valor_total, usuario)
VALUES
('2025-11-04 21:00:00', 45.50, 1),
('2025-11-03 15:30:00', 209.90, 2);

INSERT INTO pagamento (metodo, status_, dt_pagamento, valor_total, pedido)
VALUES
('Cartão de Crédito', 'Aprovado', '2025-11-04 21:01:00', 45.50, 1),
('Pix', 'Pendente', '2025-11-03 15:31:00', 209.90, 2);

-- 5. Outras Tabelas de Ligação e Avaliação
INSERT INTO contem (livro, pedido)
VALUES
(1, 1),
(2, 2),
(3, 2);

INSERT INTO avaliacao (nota, comentario, dt_avaliacao, usuario, livro)
VALUES
(5, 'Livro incrível, mudou minha vida!', '2025-11-05', 1, 1),
(4, 'Muito bom, mas o filme é diferente.', '2025-11-06', 2, 2);