<?php
// ARQUIVO: public/api/biblioteca/gerenciarLivros.php

ob_start();
session_start();

// Imports
require_once '../conection/conectionBD.php';
require_once '../classes/Biblioteca.php'; // Inclui a nova classe

header('Content-Type: application/json');

// Segurança
if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

// Instancia a classe
$biblioteca = new Biblioteca($con);
$method = $_SERVER['REQUEST_METHOD'];

// --- GET: Listar Tudo ---
if ($method === 'GET') {
    try {
        $dados = $biblioteca->listarTudo();
        echo json_encode(['success' => true] + $dados); // Junta o success com os arrays
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// --- POST: Ações de Escrita ---
if ($method === 'POST') {
    ob_clean();
    
    $action = $_POST['action'] ?? '';
    $response = ['success' => false, 'error' => 'Ação inválida'];

    // 1. LIVROS (Adicionar/Editar)
    if ($action === 'add' || $action === 'edit') {
        $response = $biblioteca->salvarLivro($_POST, $_FILES);
    }
    // 2. LIVROS (Excluir)
    elseif ($action === 'delete') {
        $response = $biblioteca->excluirLivro($_POST['livro_id']);
    }
    // 3. CATEGORIAS
    elseif ($action === 'add_categoria') {
        $response = $biblioteca->gerenciarAuxiliar('categoria', 'add', null, $_POST['nome_categoria']);
    }
    elseif ($action === 'delete_categoria') {
        $response = $biblioteca->gerenciarAuxiliar('categoria', 'delete', $_POST['id_categoria']);
    }
    // 4. AUTORES
    elseif ($action === 'add_autor') {
        $response = $biblioteca->gerenciarAuxiliar('autor', 'add', null, $_POST['nome_autor']);
    }
    elseif ($action === 'delete_autor') {
        $response = $biblioteca->gerenciarAuxiliar('autor', 'delete', $_POST['id_autor']);
    }

    echo json_encode($response);
    exit;
}
?>