<?php
/* Começando a pesquisa */
require_once 'conection/conectionBD.php';

if (!isset($_POST['pesquisa'])) {
    die("Nenhum termo de pesquisa fornecido.");
};

$pesquisa = $_POST['pesquisa'];

$sql = "SELECT * FROM livros WHERE titulo LIKE $pesquisa OR autor LIKE $pesquisa OR genero LIKE $pesquisa OR livro LIKE $pesquisa";
$result = mysqli_query($conexion, $sql);



$sql = "SELECT 1 FROM usuarios WHERE email = ? LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$emailParaVerificar]);



?>