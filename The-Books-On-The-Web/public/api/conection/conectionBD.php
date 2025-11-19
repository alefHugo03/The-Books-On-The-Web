<?php
$server = "localhost";
$user = "root";
$password = "";
$bd = "biblioteca_BD";

$con = mysqli_connect($server, $user, $password, $bd,3306);

if (!$con) {die("Erro de conexão: " . mysqli_connect_error());}

if (!$con) {die("Falha na conexão: " . mysqli_connect_error());}


?>