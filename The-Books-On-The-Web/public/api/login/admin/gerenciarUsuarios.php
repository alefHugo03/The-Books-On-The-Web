<?php
// ARQUIVO: public/api/biblioteca/admin/gerenciarUsuarios.php

// 1. Define que a resposta é JSON
header('Content-Type: application/json');

// 2. Inicia sessão se necessário
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 3. Imports de Segurança e Banco
// Ajuste os caminhos conforme sua estrutura ("../../" sobe para "public")
require_once '../../conection/bloqueioLogin.php'; 
bloqueioAdimin(); // Garante que só admin acessa

require_once '../../conection/conectionBD.php';

// 4. IMPORTANTE: Incluir a nova Classe Admin
require_once '../classes/Admin.php'; 

// Instancia a Classe passando a conexão do banco
$admin = new Admin($con);

$id_admin_logado = $_SESSION['id_user'] ?? 0;
$response = ['success' => false, 'message' => ''];

// ==================================================================
// MÉTODO GET: Listar Usuários
// ==================================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Chama a classe para buscar os dados. Código limpo!
    $ativos = $admin->listarUsuarios($id_admin_logado, 1);
    $inativos = $admin->listarUsuarios($id_admin_logado, 0);

    echo json_encode([
        'success' => true,
        'ativos' => $ativos,
        'inativos' => $inativos
    ]);
    exit;
}

// ==================================================================
// MÉTODO POST: Criar, Editar Status, Excluir
// ==================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';

    // 1. CRIAR NOVO USUÁRIO
    if ($action === 'create') {
        // Validação simples antes de chamar a classe
        if(empty($_POST['nomeAdmin']) || empty($_POST['emailAdmin'])) {
            echo json_encode(['success' => false, 'message' => 'Preencha os campos obrigatórios.']);
            exit;
        }

        // Organiza os dados
        $dadosUsuario = [
            'nome'  => $_POST['nomeAdmin'],
            'email' => $_POST['emailAdmin'],
            'cpf'   => $_POST['cpfAdmin'],
            'data'  => $_POST['dataAdmin'], // name="dataAdmin" no HTML
            'senha' => $_POST['senhaAdmin'],
            'tipo'  => $_POST['tipo']
        ];

        // A classe cuida de hash de senha e SQL
        $response = $admin->criarUsuario($dadosUsuario);
    }
    
    // 2. DESATIVAR (Soft Delete)
    elseif ($action === 'delete') { 
        $id = $_POST['id_user'];
        if ($id == $id_admin_logado) {
            $response['message'] = "Ação negada: Não pode desativar a si mesmo.";
        } else {
            // Status 0 = Inativo
            $response = $admin->alterarStatus($id, 0);
        }
    }
    
    // 3. ATIVAR
    elseif ($action === 'activate') { 
        $id = $_POST['id_user'];
        // Status 1 = Ativo
        $response = $admin->alterarStatus($id, 1);
    }
    
    // 4. EXCLUIR PERMANENTEMENTE (Hard Delete)
    elseif ($action === 'delete_permanent') { 
        $id = $_POST['id_user'];
        if ($id == $id_admin_logado) {
            $response['message'] = "Ação negada: Não pode se excluir.";
        } else {
            $response = $admin->excluirPermanente($id);
        }
    }

    echo json_encode($response);
    exit;
}
?>