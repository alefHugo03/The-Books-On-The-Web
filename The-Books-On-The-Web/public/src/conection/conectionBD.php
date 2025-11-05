<?php
$server = "localhost";
$user = "root";
$password = "";
$bd = "biblioteca_BD";

$con = mysqli_connect($server, $user, $password, $bd);

if (!$con) {
    die("Erro de conexão: " . mysqli_connect_error());
}
?>