<?php
// ARQUIVO: public/api/login/processar_login.php

// Imports
require_once '../conection/conectionBD.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

// Verifica conexão
if (!$con) {
    echo json_encode(['sucesso' => false, 'mensagem' => "Erro de conexão com o BD."]);
    exit;
}

// Validação básica
if (empty($_POST['email']) || empty($_POST['senha'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => "Preencha todos os campos."]);
    exit;
}

// Instancia e usa a classe
$auth = new Auth($con);
$resultado = $auth->login($_POST['email'], $_POST['senha']);

echo json_encode($resultado);
exit;
?>