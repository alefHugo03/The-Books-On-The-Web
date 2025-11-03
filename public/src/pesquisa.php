<?php
/* Começando a pesquisa */
require_once 'conection/conectionBD.php';

if (!isset($_GET['pesquisa'])) {
    die("Nenhum termo de pesquisa fornecido.");
};

$pesquisa = $-$_GET['pesquisa'];

$sql = "SELECT * FROM livros WHERE titulo LIKE $pesquisa OR autor LIKE $pesquisa OR genero LIKE $pesquisa OR livro LIKE $pesquisa";
$result = mysqli_query($conexion, $sql);


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://localhost/ProjetoM2/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg"  type="image/x-icon" class="favicon">
    <title>The Books On The Web</title>
</head>
<body>
    <header id="header-placeholder"></header>
            
    <main>

    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
</body>
<script src="scripts/script.js"></script>
</html>