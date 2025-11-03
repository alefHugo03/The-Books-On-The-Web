<?php
$server = "localhost";
$user = "root";
$password = "";
$bd = "biblioteca_BD";

$conexion = mysqli_connect($server, $user, $password, $bd);

if (!$conexion) {
    die("Erro de conexão: " . mysqli_connect_error());
}
?>