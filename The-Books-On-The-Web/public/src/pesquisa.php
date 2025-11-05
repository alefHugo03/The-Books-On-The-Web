<?php
require_once './conection/conectionBD.php';

if (!$con) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$termo_pesquisa = $_GET['pesquisa'];
$sql = "SELECT * FROM livro WHERE titulo LIKE ?";

$stmt = mysqli_prepare($con, $sql);

if ($stmt === false) {
    die("ERRO NO PREPARE: " . mysqli_error($con));
}

$termo_like = "%" . $termo_pesquisa . "%";
mysqli_stmt_bind_param($stmt, "s", $termo_like);

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

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
        
        <div class="container-pesquisa" style="padding: 20px; max-width: 800px; margin: auto;">

            <h4>Você pesquisou por: "<?php echo htmlspecialchars($termo_pesquisa);?>"</h4>
            <hr>
            <div class="resposta-pesquisa">
                <?php

                    if (!mysqli_num_rows($resultado) > 0) {
                        echo '<p class="nao-encontrado" style="color: red; font-weight: bold;">';
                        echo 'Nenhum livro encontrado com esse termo. Tente novamente.';
                        echo '</p>';
                    }
                        
                    while ($livro = mysqli_fetch_assoc($resultado)) {
                            echo '<div class="livro-resultado" style="margin-bottom: 25px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">';
                            echo '<h3>' . htmlspecialchars($livro['titulo']) . '</h3>';
                            echo '<p>' . htmlspecialchars($livro['descricao']) . '</p>';
                            echo '<p><strong>Preço: R$ ' . number_format($livro['preco'], 2, ',', '.') . '</strong></p>';
                            
                            echo '</div>';
                        }                    
                ?>
                <button type="button" id="btnVoltar" class="btn-menu"><a href="index.php">Voltar</a></button>
            </div>
        </div>

    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
</body>
<script src="scripts/script.js"></script>
</html>