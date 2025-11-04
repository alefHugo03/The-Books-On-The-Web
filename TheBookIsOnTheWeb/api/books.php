<?php

// Inserir um novo livro
function createBook($titulo, $autor, $preco, $imagem = '', $descricao = '') {
    global $conn;
    
    $sql = "INSERT INTO livro (titulo, autor, preco, imagem, descricao) 
            VALUES ('$titulo', '$autor', '$preco', '$imagem', '$descricao')";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Erro ao inserir livro: " . mysqli_error($conn));
    }
    return mysqli_insert_id($conn);
}

// Buscar todos os livros
function getAllBooks() {
    global $conn;
    
    $sql = "SELECT * FROM livro ORDER BY titulo";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Erro ao buscar livros: " . mysqli_error($conn));
    }
    
    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    return $books;
}

// Buscar um livro específico
function getBookById($id) {
    global $conn;
    
    $sql = "SELECT * FROM livro WHERE id = $id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Erro ao buscar livro: " . mysqli_error($conn));
    }
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

// Atualizar livro
function updateBook($id, $titulo, $autor, $preco, $imagem = '', $descricao = '') {
    global $conn;
    
    $sql = "UPDATE livro SET 
            titulo = '$titulo',
            autor = '$autor',
            preco = '$preco',
            imagem = '$imagem',
            descricao = '$descricao'
            WHERE id = $id";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Erro ao atualizar livro: " . mysqli_error($conn));
    }
    return mysqli_affected_rows($conn) > 0;
}

// Excluir um livro
function deleteBook($id) {
    global $conn;
    
    $sql = "DELETE FROM livro WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Erro ao excluir livro: " . mysqli_error($conn));
    }
    return mysqli_affected_rows($conn) > 0;
}

// buscar livros por título ou autor
function searchBooks($termo) {
    global $conn;
    
    $sql = "SELECT * FROM livro 
            WHERE titulo LIKE '%$termo%' 
            OR autor LIKE '%$termo%' 
            ORDER BY titulo";
    
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Erro ao pesquisar livros: " . mysqli_error($conn));
    }
    
    $books = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
    }
    return $books;
}
?>