<?php

require_once '../conection/conectionBD.php'; 

header('Content-Type: application/json');

$resposta = [
    'sucesso' => false,
    'mensagem' => '',
    'redirect_url' => ''
];

function validarAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header("Location: /ProjetoM2/The-Books-On-The-Web/public/templates/login/entrada.html");
        exit;
    }

    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
        header("Location: painel_logado.php");
        exit;
    }
    return $_SESSION['id_user'];
}

function criarUsuario(){
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
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
}