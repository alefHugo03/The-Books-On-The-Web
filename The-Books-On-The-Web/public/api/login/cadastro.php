<?php
// ARQUIVO: public/api/login/cadastro.php

require_once '../conection/conectionBD.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

if (!$con) {
    echo json_encode(['sucesso' => false, 'mensagem' => "Erro de conexão."]);
    exit;
}

// Prepara os dados
$dadosUsuario = [
    'nome'  => $_POST['nome'],
    'email' => $_POST['email'],
    'cpf'   => $_POST['cpf'],
    'data'  => $_POST['data'], // Confirme se no HTML o name é 'data' ou 'data_nascimento'
    'senha' => $_POST['senha']
];

// Instancia e cadastra como 'cliente' (padrão)
$auth = new Auth($con);
$resultado = $auth->cadastrar($dadosUsuario, 'cliente');

echo json_encode($resultado);
exit;
?>