<?php
header('Content-Type: application/json');

// CAMINHOS CORRIGIDOS BASEADOS NA PASTA api/login/admin/
require_once '../../conection/bloqueioLogin.php'; 
bloqueioAdimin(); 

require_once '../../conection/conectionBD.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_admin_logado = $_SESSION['id_user'] ?? 0;
$response = ['success' => false, 'message' => ''];

// --- MÉTODO GET: LER DADOS ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // Busca Ativos
    $sql_ativos = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 1";
    $stmt = mysqli_prepare($con, $sql_ativos);
    mysqli_stmt_bind_param($stmt, "i", $id_admin_logado);
    mysqli_stmt_execute($stmt);
    $ativos = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    // Busca Inativos
    $sql_inativos = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 0";
    $stmt2 = mysqli_prepare($con, $sql_inativos);
    mysqli_stmt_bind_param($stmt2, "i", $id_admin_logado);
    mysqli_stmt_execute($stmt2);
    $inativos = mysqli_fetch_all(mysqli_stmt_get_result($stmt2), MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'ativos' => $ativos,
        'inativos' => $inativos
    ]);
    exit;
}

// --- MÉTODO POST: AÇÕES ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $nome = $_POST['nomeAdmin'];
        $email = $_POST['emailAdmin'];
        $cpf = $_POST['cpfAdmin'];
        $data = $_POST['dataAdmin'];
        $senha = $_POST['senhaAdmin'];
        $tipo = $_POST['tipo'];
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssss', $data, $nome, $email, $hash, $cpf, $tipo);

        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = "Usuário criado com sucesso!";
        } else {
            $response['message'] = (mysqli_errno($con) == 1062) ? "E-mail ou CPF já em uso." : "Erro SQL: " . mysqli_error($con);
        }
    }
    elseif ($action === 'delete') {
        $id = $_POST['id_user'];
        if ($id == $id_admin_logado) {
            $response['message'] = "Não pode desativar a si mesmo.";
        } else {
            $stmt = mysqli_prepare($con, "UPDATE usuarios SET is_active = 0 WHERE id_user = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = "Usuário desativado.";
            }
        }
    }
    elseif ($action === 'activate') {
        $id = $_POST['id_user'];
        $stmt = mysqli_prepare($con, "UPDATE usuarios SET is_active = 1 WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = "Usuário ativado.";
        }
    }
    elseif ($action === 'delete_permanent') {
        $id = $_POST['id_user'];
        $stmt = mysqli_prepare($con, "DELETE FROM usuarios WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = "Usuário excluído permanentemente.";
        } else {
            $response['message'] = "Erro ao excluir (verifique dependências).";
        }
    }

    echo json_encode($response);
    exit;
}
?>