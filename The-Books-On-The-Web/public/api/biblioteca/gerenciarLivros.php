<?php
// ARQUIVO: public/api/biblioteca/gerenciarLivros.php

// 1. Prepara o terreno (Limpa buffer e inicia sessão)
ob_start();
session_start();

// 2. Define que a resposta será JSON
header('Content-Type: application/json; charset=utf-8');

// Função para encerrar com erro limpo
function enviarJson($dados) {
    ob_clean(); // Apaga qualquer HTML ou Warning que tenha aparecido antes
    echo json_encode($dados);
    exit;
}

try {
    // 3. Verifica Permissão
    if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
        enviarJson(['success' => false, 'error' => 'Acesso negado. Login de Admin necessário.']);
    }

    // 4. Inclui Dependências
    // Caminho: estamos em api/biblioteca, precisamos voltar para api/conection e api/classes
    $caminhoConexao = '../conection/conectionBD.php';
    $caminhoClasse = '../classes/Biblioteca.php';

    if (!file_exists($caminhoConexao)) throw new Exception("Arquivo de conexão não encontrado.");
    if (!file_exists($caminhoClasse)) throw new Exception("Arquivo da classe Biblioteca não encontrado.");

    require_once $caminhoConexao;
    require_once $caminhoClasse;

    if (!isset($con)) throw new Exception("Falha na conexão com o banco de dados.");

    // 5. Processa a Requisição
    $biblioteca = new Biblioteca($con);
    $method = $_SERVER['REQUEST_METHOD'];

    // --- GET: Buscar Dados ---
    if ($method === 'GET') {
        $dados = $biblioteca->listarTudo();
        enviarJson(['success' => true] + $dados);
    }

    // --- POST: Salvar ou Excluir ---
    if ($method === 'POST') {
        $action = $_POST['action'] ?? '';
        $response = ['success' => false, 'error' => 'Ação desconhecida'];

        // A. LIVROS
        if ($action === 'add' || $action === 'edit') {
            $response = $biblioteca->salvarLivro($_POST, $_FILES);
        }
        elseif ($action === 'delete') {
            $response = $biblioteca->excluirLivro($_POST['livro_id']);
        }
        
        // B. CATEGORIAS
        elseif (strpos($action, '_categoria') !== false) {
            // Remove '_categoria' para sobrar só 'add' ou 'delete'
            $act = str_replace('_categoria', '', $action); 
            $id = $_POST['id_categoria'] ?? null;
            $nome = $_POST['nome_categoria'] ?? null;
            $response = $biblioteca->gerenciarAuxiliar('categoria', $act, $id, $nome);
        }
        
        // C. AUTORES
        elseif (strpos($action, '_autor') !== false) {
            $act = str_replace('_autor', '', $action);
            $id = $_POST['id_autor'] ?? null;
            $nome = $_POST['nome_autor'] ?? null;
            $response = $biblioteca->gerenciarAuxiliar('autor', $act, $id, $nome);
        }
        
        // D. EDITORAS (Isso faltava!)
        elseif (strpos($action, '_editora') !== false) {
            $act = str_replace('_editora', '', $action);
            $id = $_POST['id_editora'] ?? null;
            $nome = $_POST['nome_editora'] ?? null;
            $response = $biblioteca->gerenciarAuxiliar('editora', $act, $id, $nome);
        }

        enviarJson($response);
    }

} catch (Exception $e) {
    enviarJson(['success' => false, 'error' => 'Erro interno: ' . $e->getMessage()]);
}
?>