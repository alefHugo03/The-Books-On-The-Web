<?php
// ARQUIVO: public/api/biblioteca/gerenciarUsuarios.php

// 1. Limpa qualquer lixo de HTML que possa ter sido gerado antes
ob_start(); 
session_start();

header('Content-Type: application/json; charset=utf-8');

// Função para retornar erro limpo em JSON se algo falhar
function enviarErroFatal($msg) {
    ob_clean(); 
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

try {
    // --- CORREÇÃO DOS CAMINHOS (Baseado na sua imagem) ---
    
    // 1. Conexão e Segurança (Estão em public/conection)
    // Saímos de 'biblioteca', saímos de 'api', entramos em 'conection'
    $pathSeguranca = '../../conection/bloqueioLogin.php';
    $pathConexao   = '../../conection/conectionBD.php';
    
    if (!file_exists($pathSeguranca)) throw new Exception("Arquivo não encontrado: $pathSeguranca");
    if (!file_exists($pathConexao))   throw new Exception("Arquivo não encontrado: $pathConexao");
    
    require_once $pathSeguranca; 
    bloqueioAdimin(); 

    require_once $pathConexao;

    // 2. Classe Admin (Está em public/api/classes)
    // Saímos de 'biblioteca', entramos em 'classes' (estão na mesma pasta 'api')
    $pathClasse = '../../classes/Admin.php';
    
    if (!file_exists($pathClasse)) throw new Exception("Arquivo não encontrado: $pathClasse");
    require_once $pathClasse; 

    // --- FIM DOS INCLUDES ---

    // Verificação da conexão
    if (!isset($con)) {
        throw new Exception('Erro crítico: Conexão com banco ($con) não existe.');
    }

    $admin = new Admin($con);
    $id_admin_logado = $_SESSION['id_user'] ?? 0;

    // --- GET ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $ativos = $admin->listarUsuarios($id_admin_logado, 1);
        $inativos = $admin->listarUsuarios($id_admin_logado, 0);

        ob_clean(); // Garante JSON limpo
        echo json_encode([
            'success' => true,
            'ativos' => $ativos,
            'inativos' => $inativos
        ]);
        exit;
    }

    // --- POST ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $response = ['success' => false, 'message' => 'Ação inválida'];

        if ($action === 'create') {
            if(empty($_POST['nomeAdmin']) || empty($_POST['emailAdmin'])) {
                enviarErroFatal('Preencha os campos obrigatórios.');
            }
            $dadosUsuario = [
                'nome'  => $_POST['nomeAdmin'],
                'email' => $_POST['emailAdmin'],
                'cpf'   => $_POST['cpfAdmin'],
                'data'  => $_POST['dataAdmin'],
                'senha' => $_POST['senhaAdmin'],
                'tipo'  => $_POST['tipo']
            ];
            $response = $admin->criarUsuario($dadosUsuario);
        }
        elseif ($action === 'delete') { 
            $id = $_POST['id_user'];
            if ($id == $id_admin_logado) {
                $response['message'] = "Não pode desativar a si mesmo.";
            } else {
                $response = $admin->alterarStatus($id, 0);
            }
        }
        elseif ($action === 'activate') { 
            $response = $admin->alterarStatus($_POST['id_user'], 1);
        }
        elseif ($action === 'delete_permanent') { 
            $id = $_POST['id_user'];
            if ($id == $id_admin_logado) {
                $response['message'] = "Não pode se excluir.";
            } else {
                $response = $admin->excluirPermanente($id);
            }
        }

        ob_clean();
        echo json_encode($response);
        exit;
    }

} catch (Exception $e) {
    enviarErroFatal('Erro no Servidor: ' . $e->getMessage());
}
?>