<?php
// ARQUIVO: public/api/biblioteca/admin/gerenciarUsuarios.php

header('Content-Type: application/json');

// Inicia sessão se necessário
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Imports necessários
require_once '../../conection/bloqueioLogin.php'; 
bloqueioAdimin(); // Segurança

require_once '../../conection/conectionBD.php';
require_once '../classes/Admin.php'; // IMPORTANTE: Caminho para sua nova classe

// Instancia a Classe passando a conexão do banco
// Agora o objeto $admin tem todo o poder da classe
$admin = new Admin($con);

$id_admin_logado = $_SESSION['id_user'] ?? 0;
$response = ['success' => false, 'message' => ''];

// --- MÉTODO GET (Listar) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Olha como ficou limpo! Sem SQL aqui no meio.
    $ativos = $admin->listarUsuarios($id_admin_logado, 1);
    $inativos = $admin->listarUsuarios($id_admin_logado, 0);

    echo json_encode([
        'success' => true,
        'ativos' => $ativos,
        'inativos' => $inativos
    ]);
    exit;
}

// --- MÉTODO POST (Ações) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';

    // 1. CRIAR
    if ($action === 'create') {
        // Validação básica
        if(empty($_POST['nomeAdmin']) || empty($_POST['emailAdmin'])) {
            echo json_encode(['success' => false, 'message' => 'Preencha os campos obrigatórios.']);
            exit;
        }

        // Prepara os dados num array organizado
        $dadosUsuario = [
            'nome'  => $_POST['nomeAdmin'],
            'email' => $_POST['emailAdmin'],
            'cpf'   => $_POST['cpfAdmin'],
            'data'  => $_POST['dataAdmin'],
            'senha' => $_POST['senhaAdmin'],
            'tipo'  => $_POST['tipo']
        ];

        // Chama a classe para criar
        $response = $admin->criarUsuario($dadosUsuario);
    }
    
    // 2. DESATIVAR (Soft Delete)
    elseif ($action === 'delete') { 
        $id = $_POST['id_user'];
        if ($id == $id_admin_logado) {
            $response['message'] = "Não pode desativar a si mesmo.";
        } else {
            // Passa 0 para status inativo
            $response = $admin->alterarStatus($id, 0);
        }
    }
    
    // 3. ATIVAR
    elseif ($action === 'activate') { 
        // Passa 1 para status ativo
        $response = $admin->alterarStatus($_POST['id_user'], 1);
    }
    
    // 4. EXCLUIR PERMANENTE
    elseif ($action === 'delete_permanent') { 
        $id = $_POST['id_user'];
        if ($id == $id_admin_logado) {
            $response['message'] = "Não pode se excluir.";
        } else {
            $response = $admin->excluirPermanente($id);
        }
    }

    echo json_encode($response);
    exit;
}
?>