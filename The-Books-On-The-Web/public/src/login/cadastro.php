<?php
require_once '../conection/conectionBD.php'; // (Verifique este caminho)

header('Content-Type: application/json');

$resposta = [
    'sucesso' => false,
    'mensagem' => '',
    'redirect_url' => ''
];

if (!$con) {
    $resposta['mensagem'] = "Falha grave na conexão com o BD.";
    echo json_encode($resposta);
    exit;
}

// ---- CORREÇÃO DO NOME DA VARIÁVEL ----
// O FormData (JS) envia 'data_nascimento' baseado no 'name' do HTML.
$data_nascimento = $_POST['data']; 
$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha_digitada = $_POST['senha'];

$hash = password_hash($senha_digitada, PASSWORD_DEFAULT);

$sql = 'INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?, "admin")';
$stmt = mysqli_prepare($con, $sql);

// O bind está correto (5 placeholders, 5 variáveis)
mysqli_stmt_bind_param($stmt, 'sssss', $data_nascimento, $nome, $email, $hash, $cpf);

$executou_com_sucesso = mysqli_stmt_execute($stmt);

// ---- LÓGICA CORRIGIDA ----
if ($executou_com_sucesso) {
    // SUCESSO!
    $resposta['sucesso'] = true;
    $resposta['mensagem'] = "Admin cadastrado com segurança!";
    $resposta['redirect_url'] = "templates/login/entrada.html";
} else {
    // FALHA! (Agora sim, vamos checar o motivo)
    if (mysqli_errno($con) == 1062) { // 1062 = Erro de duplicata
        $resposta['mensagem'] = "Ops! Este e-mail ou CPF já está em uso.";
    } else {
        $resposta['mensagem'] = "Ops! Não foi possível cadastrar: " . mysqli_error($con);
    }
    // 'sucesso' já é 'false' por padrão
}

// Envie a resposta JSON e termine
echo json_encode($resposta);
exit;
?>