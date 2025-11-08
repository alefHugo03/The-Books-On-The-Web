<?php
require_once '../conection/conectionBD.php'; 

class classAdmin
{
    private $data_nascimento;
    private $nome;
    private $email;
    private $senha;
    private $cpf;
    private $tipo;

    public function inserirDados($data_nascimento,$nome,$email,$senha,$cpf,$tipo)
    {

        $sql = 'INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?, "admin")';
        $stmt = mysqli_prepare($con, $sql);

        mysqli_stmt_bind_param($stmt, 'sssss', $data_nascimento, $nome, $email, $hash, $cpf);

        return mysqli_stmt_execute($stmt);
    }
}

// AÇÃO 1: CRIAR UM NOVO USUÁRIO
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        // CORREÇÃO: O 'name' do seu formulário é 'data', mas a coluna é 'data_nascimento'
        // Mudei o formulário (lá embaixo) para enviar 'data_nascimento'
        $data_nascimento = $_POST['data_nascimento']; 
        $senha_digitada = $_POST['senha'];
        $tipo = $_POST['tipo'];

        $hash = password_hash($senha_digitada, PASSWORD_DEFAULT);

        $sql_create = 'INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt_create = mysqli_prepare($con, $sql_create);
        mysqli_stmt_bind_param($stmt_create, 'ssssss', $data_nascimento, $nome, $email, $hash, $cpf, $tipo);
        
        if (mysqli_stmt_execute($stmt_create)) {
            $mensagem_feedback = "Usuário '$nome' criado com sucesso!";
        } else {
             if (mysqli_errno($con) == 1062) {
                $mensagem_feedback = "Erro: Este e-mail ou CPF já está em uso.";
            } else {
                $mensagem_feedback = "Erro ao criar usuário: " . mysqli_error($con);
            }
        }
    }

    // AÇÃO 2: "DELETAR" (DESATIVAR) UM USUÁRIO - (SOFT DELETE)
    // (O 'action' que você chamou de 'delete')
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id_para_desativar = $_POST['id_user_to_delete'];

        if ($id_para_desativar == $id_admin_logado) {
            $mensagem_feedback = "Erro: Você não pode desativar a si mesmo!";
        } else {
            $sql_delete = "UPDATE usuarios SET is_active = 0 WHERE id_user = ?";
            $stmt_delete = mysqli_prepare($con, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, 'i', $id_para_desativar);
            
            if (mysqli_stmt_execute($stmt_delete)) {
                $mensagem_feedback = "Usuário desativado com sucesso!";
            } else {
                $mensagem_feedback = "Erro ao desativar usuário: " . mysqli_error($con);
            }
        }
    }
    
    // AÇÃO 3: ATIVAR UM USUÁRIO (NOVO)
    if (isset($_POST['action']) && $_POST['action'] === 'activate') {
        $id_para_ativar = $_POST['id_user_to_activate'];
        
        $sql_activate = "UPDATE usuarios SET is_active = 1 WHERE id_user = ?";
        $stmt_activate = mysqli_prepare($con, $sql_activate);
        mysqli_stmt_bind_param($stmt_activate, 'i', $id_para_ativar);
        
        if (mysqli_stmt_execute($stmt_activate)) {
            $mensagem_feedback = "Usuário reativado com sucesso!";
        } else {
            $mensagem_feedback = "Erro ao reativar usuário: " . mysqli_error($con);
        }
    }
    
    // AÇÃO 4: DELETAR PERMANENTEMENTE (NOVO)
    // (Isso só vai funcionar se você usou o SQL com ON DELETE CASCADE)
    if (isset($_POST['action']) && $_POST['action'] === 'delete_permanent') {
        $id_para_deletar = $_POST['id_user_to_delete'];
        
        if ($id_para_deletar == $id_admin_logado) {
            $mensagem_feedback = "Erro: Você não pode deletar a si mesmo!";
        } else {
            $sql_perm_delete = "DELETE FROM usuarios WHERE id_user = ?";
            $stmt_perm_delete = mysqli_prepare($con, $sql_perm_delete);
            mysqli_stmt_bind_param($stmt_perm_delete, 'i', $id_para_deletar);
            
            if (mysqli_stmt_execute($stmt_perm_delete)) {
                $mensagem_feedback = "Usuário DELETADO PERMANENTEMENTE!";
            } else {
                // Se você não usou o ON DELETE CASCADE, este erro vai aparecer
                $mensagem_feedback = "Erro ao deletar: " . mysqli_error($con);
            }
        }
    }
}

// 4. LER OS USUÁRIOS (SEPARADAMENTE)
// Lista de ATIVOS
$sql_select_ativos = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 1";
$stmt_select_ativos = mysqli_prepare($con, $sql_select_ativos);
mysqli_stmt_bind_param($stmt_select_ativos, "i", $id_admin_logado);
mysqli_stmt_execute($stmt_select_ativos);
$resultado_usuarios_ativos = mysqli_stmt_get_result($stmt_select_ativos);

// Lista de INATIVOS
$sql_select_of = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 0";
$stmt_select_of = mysqli_prepare($con, $sql_select_of);
mysqli_stmt_bind_param($stmt_select_of, "i", $id_admin_logado);
mysqli_stmt_execute($stmt_select_of);
$resultado_usuarios_desligados = mysqli_stmt_get_result($stmt_select_of);



?>