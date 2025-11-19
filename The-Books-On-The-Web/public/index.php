<?php
session_start();
require_once './api/conection/conectionBD.php';
require_once './api/conection/functionsBD.php';

// --- LÓGICA DE PAGINAÇÃO ---
$itens_por_pagina = 6; // Defina quantos livros quer por página
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;

// Calcula o ponto de partida para o SQL
$inicio = ($pagina_atual - 1) * $itens_por_pagina;

// Busca os livros com limite
$resultado = procurarLivros($inicio, $itens_por_pagina);

// Calcula totais para gerar os botões
$total_livros = contarTotalLivros();
$total_paginas = ceil($total_livros / $itens_por_pagina);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <base href="http://192.168.0.136:80/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
<<<<<<< HEAD
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Home | TBOTW </title>
    
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    
    <link rel="stylesheet" href="styles/stylephone.css?v=<?php echo time(); ?>">
    
=======
    <title>Home | TBOTW </title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
>>>>>>> f3388bb974763adc3095a55d3d92c0b5389773d6
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon" class="favicon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js"></script>
</head>

<body>
    <header>
        <div class="cabecalho header-cima">
            <div class="empresa">
                <a href="index.php" class="nome-empresa">
                    <h1>The Books<br> On The Web</h1>
                </a>
                <a href="index.php" class="nome-empresa"><img src="styles/img/favicon.svg" class="imagem-empresa"></a>
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
            <div class="area-cadastro"><?php exibirMenuAutenticacao(); ?></div>
        </div>
        <div class="cabecalho header-baixo">
            <nav class="opcoes">
                <a href="index.php" class="item-menu">Home</a>
                <a href="templates/biblioteca/resumo.html" class="item-menu">Sobre</a>
                <?php exibirBotoesCliente(); ?>
                <?php exibirBotoesAdimin(); ?>
            </nav>
        </div>
    </header>

    <main>
        <div class="container-vitrine" style="padding: 20px; max-width: 1000px; margin: auto;">
            <h2>Destaques Da Biblioteca</h2>
            <hr style="margin-bottom: 20px;">

            <div class="lista-livros">
                <?php
                if (mysqli_num_rows($resultado) > 0) {
                    while ($livro = mysqli_fetch_assoc($resultado)) {
                        $caminhoPdf = '../database/pdfs/' . $livro['pdf'];

<<<<<<< HEAD
                        echo '<a href="templates/biblioteca/livros.php?id=' . $livro['id_livro'] . '" style="text-decoration:none; color:inherit;">';
                        echo '<div class="livro-card">';
=======
                        echo '<a href="templates\biblioteca\livros.php?id=' . $livro['id_livro'] . '" style="text-decoration:none; color:inherit;">';

                        echo '<div class="livro-card">';

                        // CAPA
>>>>>>> f3388bb974763adc3095a55d3d92c0b5389773d6
                        echo '<div class="capa-wrapper">';
                        if (!empty($livro['pdf'])) {
                            echo '<canvas class="pdf-thumb" data-url="' . $caminhoPdf . '"></canvas>';
                        } else {
                            echo '<div class="sem-capa">Sem Capa</div>';
                        }
                        echo '</div>';
                        echo '<div class="info-livro">';
                        echo '<h3>' . htmlspecialchars($livro['titulo']) . '</h3>';
                        echo '<p>' . htmlspecialchars($livro['descricao']) . '</p>';
                        echo '<span class="categoria-tag">' . htmlspecialchars($livro['nome_categoria']) . '</span>';
                        echo '</div>';
                        echo '</div>'; 
                        echo '</a>';
                    }
                } else {
                    echo '<p>Nenhum livro encontrado.</p>';
                }
                ?>
            </div>

            <?php if ($total_paginas > 1): ?>
            <div class="paginacao-container">
                <div class="paginacao">
                    <?php
<<<<<<< HEAD
                    if ($pagina_atual > 1) {
                        echo '<a href="index.php?pagina=' . ($pagina_atual - 1) . '" class="btn-pag anterior"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></a>';
                    }
=======
                    // Botão Anterior
                    if ($pagina_atual > 1) {
                        echo '<a href="index.php?pagina=' . ($pagina_atual - 1) . '" class="btn-pag anterior">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                              </a>';
                    }

                    // Botões Numéricos
>>>>>>> f3388bb974763adc3095a55d3d92c0b5389773d6
                    for ($i = 1; $i <= $total_paginas; $i++) {
                        $classe_ativa = ($i == $pagina_atual) ? 'active' : '';
                        echo '<a href="index.php?pagina=' . $i . '" class="btn-pag ' . $classe_ativa . '">' . $i . '</a>';
                    }
<<<<<<< HEAD
                    if ($pagina_atual < $total_paginas) {
                        echo '<a href="index.php?pagina=' . ($pagina_atual + 1) . '" class="btn-pag proximo"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></a>';
=======

                    // Botão Próximo
                    if ($pagina_atual < $total_paginas) {
                        echo '<a href="index.php?pagina=' . ($pagina_atual + 1) . '" class="btn-pag proximo">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                              </a>';
>>>>>>> f3388bb974763adc3095a55d3d92c0b5389773d6
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
            </div>
    </main>

    <footer class="caixa-footer">
        <p>© 2025 The Books On The Web. Todos os direitos reservados.</p>
    </footer>
    <script src="scripts/script.js"></script>
    <script src="scripts/pdfRender.js"></script>
</body>
<<<<<<< HEAD
=======

>>>>>>> f3388bb974763adc3095a55d3d92c0b5389773d6
</html>