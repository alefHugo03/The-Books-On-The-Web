<?php
$servername = 'localhost';
$database = 'bookonweb';
$username = 'root';
$password = '';

// Habilitar para receber feedback de conexão
$db_debug = false;

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
} else {
    mysqli_set_charset($conn, 'utf8mb4');
    if ($db_debug) {
        echo "Conexão com o banco de dados estabelecida com sucesso.";
    }
}
?>
