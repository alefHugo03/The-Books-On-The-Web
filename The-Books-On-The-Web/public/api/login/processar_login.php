<?php
session_start();
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

if (empty($_POST['email']) || empty($_POST['senha'])) {
    $resposta['mensagem'] = "Email e senha são obrigatórios.";
    echo json_encode($resposta);
    exit;
}

$email = $_POST['email'];
$senha_digitada = $_POST['senha'];


$sql = "SELECT id_user, senha, tipo FROM usuarios WHERE email = ? AND is_active = 1 LIMIT 1";
$stmt = mysqli_prepare($con, $sql);

if ($stmt === false) {
    $resposta['mensagem'] = "Erro interno do servidor (prepare).";
    echo json_encode($resposta);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);


if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {
    
    $_SESSION['logado'] = true;
    $_SESSION['id_user'] = $usuario['id_user'];
    $_SESSION['email_user'] = $email;

    $_SESSION['tipo'] = $usuario['tipo'];

    $resposta['sucesso'] = true;
    $resposta['mensagem'] = "Login bem-sucedido!";
    $resposta['redirect_url'] = "templates/login/painel_logado.php";

} else {
    $resposta['sucesso'] = false;
    $resposta['mensagem'] = "Credenciais inválidas. Tente novamente.";
}

echo json_encode($resposta);
exit;

?>