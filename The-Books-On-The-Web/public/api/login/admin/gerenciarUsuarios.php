<?php
// 1. Define que a resposta é JSON (Isso previne que o navegador tente interpretar HTML)
header('Content-Type: application/json');

// 2. Inicia a sessão PRIMEIRO
// A função bloqueioAdimin precisa ler $_SESSION, então a sessão tem que existir antes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Caminhos Ajustados (Apenas 2 níveis para voltar)
// admin (start) -> ../ (login) -> ../ (api) -> entra em conection
require_once '../../conection/bloqueioLogin.php'; 

// Verifica segurança
bloqueioAdimin(); 

// Conexão com Banco
require_once '../../conection/conectionBD.php';

// Verifica se a conexão funcionou
if (!isset($con)) {
    echo json_encode(['success' => false, 'message' => 'Erro fatal: Conexão com banco não estabelecida.']);
    exit;
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
        // Verifica se os dados chegaram
        if(!isset($_POST['nomeAdmin']) || !isset($_POST['emailAdmin'])) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
            exit;
        }

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
            // Tratamento de erro específico para duplicação
            if (mysqli_errno($con) == 1062) {
                $response['message'] = "Já existe um usuário com este E-mail ou CPF.";
            } else {
                $response['message'] = "Erro no Banco: " . mysqli_error($con);
            }
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
        // Cuidado: Isso pode dar erro se o usuário tiver livros emprestados (Foreign Key)
        // O ideal é usar try/catch ou verificar erros
        $stmt = mysqli_prepare($con, "DELETE FROM usuarios WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $response['success'] = true;
            $response['message'] = "Usuário excluído permanentemente.";
        } else {
            // Erro 1451 é constraint (chave estrangeira)
            if (mysqli_errno($con) == 1451) {
                $response['message'] = "Não é possível excluir: Este usuário possui registros vinculados.";
            } else {
                $response['message'] = "Erro ao excluir: " . mysqli_error($con);
            }
        }
    }

    echo json_encode($response);
    exit;
}
?>