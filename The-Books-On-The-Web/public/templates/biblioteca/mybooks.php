<?php 
session_start();

if ( !isset($_SESSION['logado']) || $_SESSION['logado'] !== true ) {
    header("Location: /The-Books-On-The-Web/public/templates/login/entrada.html");
    exit; 
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg"  type="image/x-icon" class="favicon">
    <title>Meus Livros | TBOTW</title>
</head>
<body>
    <header id="header-placeholder"></header>
            
    <main>

    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
</body>
<script src="scripts/script.js"></script>
</html>