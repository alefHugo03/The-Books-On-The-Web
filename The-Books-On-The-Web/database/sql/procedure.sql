CREATE DATABASE Teste IF NOT EXISTS;
USE Teste;

DELIMITER //

CREATE PROCEDURE pesquisa (OUT nome, tipo, autor) 
BEGIN
    SELECT * FROM livro
    WHERE biblioteca 


END
DELIMITER;