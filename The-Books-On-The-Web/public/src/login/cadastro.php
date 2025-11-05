<?php
require_once 'conectionBD.php';

$email = $_POST['email'];
$senha_digitada = $_POST['senha']; // "senha123"

$hash = password_hash($senha_digitada, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (email, senha) VALUES (?, ?)";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ss", $email, $hash);
mysqli_stmt_execute($stmt);

echo "Usuário cadastrado com segurança!";
?>