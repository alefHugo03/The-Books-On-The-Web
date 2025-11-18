<?php
// ARQUIVO: public/api/biblioteca/gerenciarLivros.php

// 1. Inicia Buffer e Sessão (Previne erros de HTML vazando antes do JSON)
ob_start(); 
session_start();

// 2. Define Header JSON
header('Content-Type: application/json; charset=utf-8');

// Função auxiliar para parar o script e enviar erro limpo
function enviarErro($msg) {
    ob_clean(); // Limpa qualquer lixo (warnings, br, b) do buffer
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

try {
    // --- CORREÇÃO DOS CAMINHOS (Baseado na sua imagem) ---
    // Estamos em: public/api/biblioteca/
    // Voltamos 1 nível (../) para chegar em: public/api/
    
    // 3. Incluir Conexão
    $caminhoConexao = '../conection/conectionBD.php';
    if (!file_exists($caminhoConexao)) {
        throw new Exception("Arquivo de conexão não encontrado em: $caminhoConexao");
    }
    require_once $caminhoConexao;

    // 4. Incluir Classe Biblioteca
    $caminhoClasse = '../classes/Biblioteca.php';
    if (!file_exists($caminhoClasse)) {
        throw new Exception("Arquivo da Classe Biblioteca não encontrado em: $caminhoClasse");
    }
    require_once $caminhoClasse;

    // 5. Verificações de Segurança
    if (!isset($con)) {
        throw new Exception('Erro crítico: Variável de conexão ($con) não existe.');
    }

    if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
        enviarErro('Acesso negado. Faça login como administrador.');
    }

    // 6. Instancia a Classe
    $biblioteca = new Biblioteca($con);
    $method = $_SERVER['REQUEST_METHOD'];

    // ==================================================================
    // GET: Listar Dados (Tabelas e Selects)
    // ==================================================================
    if ($method === 'GET') {
        $dados = $biblioteca->listarTudo();
        ob_clean(); // Garante resposta limpa
        echo json_encode(['success' => true] + $dados);
        exit;
    }

    // ==================================================================
    // POST: Adicionar, Editar, Excluir
    // ==================================================================
    if ($method === 'POST') {
        $action = $_POST['action'] ?? '';
        $response = ['success' => false, 'error' => 'Ação inválida'];

        // A. LIVROS
        if ($action === 'add' || $action === 'edit') {
            $response = $biblioteca->salvarLivro($_POST, $_FILES);
        }
        elseif ($action === 'delete') {
            $response = $biblioteca->excluirLivro($_POST['livro_id']);
        }
        
        // B. CATEGORIAS
        elseif ($action === 'add_categoria') {
            $response = $biblioteca->gerenciarAuxiliar('categoria', 'add', null, $_POST['nome_categoria']);
        }
        elseif ($action === 'delete_categoria') {
            $response = $biblioteca->gerenciarAuxiliar('categoria', 'delete', $_POST['id_categoria']);
        }
        
        // C. AUTORES
        elseif ($action === 'add_autor') {
            $response = $biblioteca->gerenciarAuxiliar('autor', 'add', null, $_POST['nome_autor']);
        }
        elseif ($action === 'delete_autor') {
            $response = $biblioteca->gerenciarAuxiliar('autor', 'delete', $_POST['id_autor']);
        }

        ob_clean(); // Limpa antes de enviar
        echo json_encode($response);
        exit;
    }

} catch (Exception $e) {
    // Captura erros de arquivo ou lógica e envia como JSON legível
    enviarErro('Erro no Servidor: ' . $e->getMessage());
}
?>