<?php
require_once '../conection/conectionBD.php';

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

$data_nascimento = $_POST['data']; 
$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha_digitada = $_POST['senha'];
$tipo = $_POST['tipo'];

$hash = password_hash($senha_digitada, PASSWORD_DEFAULT);

$sql = 'INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?, ?)';
$stmt = mysqli_prepare($con, $sql);

mysqli_stmt_bind_param($stmt, 'sssss', $data_nascimento, $nome, $email, $hash, $cpf, $tipo);

$executou_com_sucesso = mysqli_stmt_execute($stmt);

if ($executou_com_sucesso) {
    $resposta['sucesso'] = true;
    $resposta['mensagem'] = "Admin cadastrado com segurança!";
    $resposta['redirect_url'] = "templates/login/entrada.html";
} else {
    if (mysqli_errno($con) == 1062) {
        $resposta['mensagem'] = "Ops! Este e-mail ou CPF já está em uso.";
    } else {
        $resposta['mensagem'] = "Ops! Não foi possível cadastrar: " . mysqli_error($con);
    }
}

echo json_encode($resposta);
exit;
?>