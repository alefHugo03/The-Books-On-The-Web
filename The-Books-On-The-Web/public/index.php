<?php
session_start();
require_once './api/conection/conectionBD.php';
require_once './api/conection/functionsBD.php';
$resultado = procurarLivros();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon" class="favicon">
    <title>Home | TBOTW </title>
</head>

<body>
    <header>
        <div class="cabecalho header-cima">
            <div class="empresa">
                <a href="index.php" class="nome-empresa">
                    <h1>The Books<br> On The Web</h1>
                </a>
                <a href="index.php" class="nome-empresa"><img src="styles/img/favicon.svg" alt="imagem logo" class="imagem-empresa"></a>
            </div>

            <div class="pesquisa">
                <form action="templates/biblioteca/pesquisa.php" id="pesquisar" class="pesquisar" method="get">
                    <input type="text" class="input-pesquisa" id="campoPesquisa" placeholder="Pesquisar..." name="pesquisa">
                    <button type="submit" class="btn-pesquisa">
                        <svg width="24" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.134 17 3 13.866 3 10C3 6.134 6.134 3 10 3C13.866 3 17 6.134 17 10Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </form>
            </div>

            <div class="area-cadastro">
                <?php
                    exibirMenuAutenticacao();
                ?>
            </div>
        </div>

        <div class="cabecalho header-baixo"> 
            <nav class="opcoes"> 
                <a href="index.php" class="item-menu">Home</a>
                <a href="templates/biblioteca/resumo.html" class="item-menu">Sobre</a>
                <a href="templates/biblioteca/livros.php" class="item-menu">Serviços</a>
                
                <?php
                    exibirBotoesCliente();
                ?>
                <?php
                    exibirBotoesAdimin();
                ?>
            </nav>
        </div>
    </header>



    <main>

        <div class="container-vitrine" style="padding: 20px; max-width: 800px; margin: auto;">

            <h2>Destaques Aleatórios</h2>
            <hr>

            <?php
            if (mysqli_num_rows($resultado) > 0) {
                while ($livro = mysqli_fetch_assoc($resultado)) {
                    echo '<div class="livro-resultado" style="margin-bottom: 25px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">';
                    echo '<h3>' . htmlspecialchars($livro['titulo']) . '</h3>';
                    echo '<p>' . htmlspecialchars($livro['descricao']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhum livro encontrado no banco de dados.</p>';
            }
            ?>
        </div>

    </main>

    <footer class="caixa-footer">
        <p>© 2024 The Books On The Web. Todos os direitos reservados.</p>
    </footer>
</body>
<script src="scripts/script.js"></script>

</html>