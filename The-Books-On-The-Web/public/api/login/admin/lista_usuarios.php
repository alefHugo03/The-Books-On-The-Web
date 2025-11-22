<?php
// Define que a resposta será sempre um JSON (para o JavaScript ler)
header('Content-Type: application/json; charset=utf-8');

// 1. CONEXÕES E SEGURANÇA
// Ajuste os caminhos conforme sua estrutura de pastas (subindo 2 níveis)
require_once '../../conection/bloqueioLogin.php'; 
bloqueioAdimin(); // Bloqueia quem não é admin

require_once '../../conection/conectionBD.php';

// Inicia sessão para pegar o ID do admin logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_admin_logado = $_SESSION['id_user'] ?? 0;

// Inicializa a resposta padrão
$response = [
    'success' => false, 
    'message' => 'Erro desconhecido.'
];

// ==================================================================
// MÉTODOS DE REQUISIÇÃO
// ==================================================================

// --- GET: BUSCAR USUÁRIOS PARA A TABELA ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // Busca Usuários ATIVOS (exceto o logado)
    $sql_ativos = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 1";
    $stmt = mysqli_prepare($con, $sql_ativos);
    mysqli_stmt_bind_param($stmt, "i", $id_admin_logado);
    mysqli_stmt_execute($stmt);
    $ativos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    // Busca Usuários INATIVOS (exceto o logado)
    $sql_inativos = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 0";
    $stmt2 = mysqli_prepare($con, $sql_inativos);
    mysqli_stmt_bind_param($stmt2, "i", $id_admin_logado);
    mysqli_stmt_execute($stmt2);
    $inativos = mysqli_fetch_all(mysqli_stmt_get_result($stmt2), MYSQLI_ASSOC);

    // Retorna os dados
    echo json_encode([
        'success' => true,
        'ativos' => $ativos,
        'inativos' => $inativos
    ]);
    exit;
}

// --- POST: CRIAR, EDITAR STATUS OU DELETAR ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';

    // --------------------------------------------------------------
    // AÇÃO 1: CRIAR NOVO USUÁRIO
    // --------------------------------------------------------------
    if ($action === 'create') {
        // Coleta e limpa os dados (remove espaços extras)
        $nome = trim($_POST['nomeAdmin'] ?? '');
        $email = trim($_POST['emailAdmin'] ?? '');
        $cpf = trim($_POST['cpfAdmin'] ?? '');
        $data = $_POST['dataAdmin'] ?? '';
        $senha = $_POST['senhaAdmin'] ?? '';
        $tipo = $_POST['tipo'] ?? '';

        // --- VALIDAÇÃO NO SERVIDOR (SEGURANÇA CRÍTICA) ---
        // Impede criação de usuário vazio mesmo se burlarem o JS
        if (empty($nome) || empty($email) || empty($cpf) || empty($data) || empty($senha) || empty($tipo)) {
            echo json_encode([
                'success' => false,
                'message' => 'Erro: Todos os campos são obrigatórios. Verifique o preenchimento.'
            ]);
            exit;
        }

        // Criptografa a senha
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Query de Inserção (Padrão is_active = 1)
        $sql = "INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($con, $sql);
        
        // s = string
        mysqli_stmt_bind_param($stmt, 'ssssss', $data, $nome, $email, $hash, $cpf, $tipo);

        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = "Usuário '$nome' criado com sucesso!";
        } else {
            // Verifica erro de duplicidade (Email ou CPF já existem)
            if (mysqli_errno($con) == 1062) {
                $response['message'] = "Erro: E-mail ou CPF já cadastrados no sistema.";
            } else {
                $response['message'] = "Erro no Banco de Dados: " . mysqli_error($con);
            }
        }
    }

    // --------------------------------------------------------------
    // AÇÃO 2: DESATIVAR (SOFT DELETE)
    // --------------------------------------------------------------
    elseif ($action === 'delete') {
        $id_target = $_POST['id_user'];

        if ($id_target == $id_admin_logado) {
            $response['message'] = "Ação bloqueada: Você não pode desativar seu próprio usuário.";
        } else {
            $sql = "UPDATE usuarios SET is_active = 0 WHERE id_user = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $id_target);

            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = "Usuário desativado com sucesso.";
            } else {
                $response['message'] = "Erro ao desativar.";
            }
        }
    }

    // --------------------------------------------------------------
    // AÇÃO 3: ATIVAR
    // --------------------------------------------------------------
    elseif ($action === 'activate') {
        $id_target = $_POST['id_user'];

        $sql = "UPDATE usuarios SET is_active = 1 WHERE id_user = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id_target);

        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = "Usuário reativado com sucesso.";
        } else {
            $response['message'] = "Erro ao ativar.";
        }
    }

    // --------------------------------------------------------------
    // AÇÃO 4: EXCLUIR PERMANENTEMENTE
    // --------------------------------------------------------------
    elseif ($action === 'delete_permanent') {
        $id_target = $_POST['id_user'];

        if ($id_target == $id_admin_logado) {
            $response['message'] = "Ação bloqueada: Você não pode se excluir do sistema.";
        } else {
            $sql = "DELETE FROM usuarios WHERE id_user = ?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $id_target);

            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = "Usuário excluído permanentemente!";
            } else {
                $response['message'] = "Erro ao excluir. O usuário pode ter registros vinculados.";
            }
        }
    }

    // Retorna a resposta final em JSON
    echo json_encode($response);
    exit;
}
?>