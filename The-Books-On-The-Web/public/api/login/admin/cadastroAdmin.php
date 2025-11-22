<?php
// ARQUIVO: public/api/login/cadastroAdmin.php

require_once '../conection/conectionBD.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

// ... (Verificações de conexão iguais acima) ...

$dadosUsuario = [
    'nome'  => $_POST['nome'],
    'email' => $_POST['email'],
    'cpf'   => $_POST['cpf'],
    'data'  => $_POST['data'],
    'senha' => $_POST['senha']
];

// A única diferença é passar 'admin' aqui no final
$auth = new Auth($con);
$resultado = $auth->cadastrar($dadosUsuario, 'admin');

echo json_encode($resultado);
exit;
?>