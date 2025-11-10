<?php
session_start();

require_once './api/conection/conectionBD.php';

if (!$con) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$sql = "SELECT * FROM livro ORDER BY RAND() LIMIT 6";
$resultado = mysqli_query($con, $sql);

if ($resultado === false) {
    die("ERRO NO SQL: " . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon" class="favicon">
    <title>The Books On The Web</title>
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
                if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                    echo "<div class = 'perfil-header'>";
                    echo '<a href="templates/login/painel_logado.php" class="btn-cadastro">Perfil</a>';
                    echo '<a href="api/login/logout.php" class="btn-cadastro">Sair</a>';
                    echo "</div>";

                    echo "<div class = 'span-header'>";
                    echo '<span class="saudacao">Olá, ' . htmlspecialchars($_SESSION['email_user']) . '!</span>';
                    echo "</div>";
                } else {
                    echo "<div class = 'perfil-header'>";
                    echo '<a href="templates/login/entrada.html" class="btn-cadastro">Entrar</a>';
                    echo '<a href="templates/login/cadastro.html" class="btn-cadastro">Cadastrar-se</a>';
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="cabecalho header-baixo"> 
            <nav class="opcoes"> 
                <a href="index.php" class="item-menu">Home</a>
                <a href="templates/biblioteca/resumo.html" class="item-menu">Sobre</a>
                <a href="templates/biblioteca/livros.php" class="item-menu">Serviços</a>
                
                <?php
                if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                    echo '<a href="templates/biblioteca/mybooks.php" class="item-menu">Meus Livros</a>';
                }
                ?>
                <?php
                if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') {
                    echo '<a href="templates/biblioteca/admin/painel_admin.php" class="item-menu">Painel Admin</a>';
                }
                ?>
            </nav>
        </div>
    </header>



    <main>

        <div class="container-vitrine" style="padding: 20px; max-width: 800px; margin: auto;">

            <h2>Destaques Aleatórios</h2>
            <hr>

            <?php
            // 4. Verifica se a busca retornou alguma linha
            if (mysqli_num_rows($resultado) > 0) {

                // 5. Faz o loop (igual ao da pesquisa)
                while ($livro = mysqli_fetch_assoc($resultado)) {

                    // Imprime o HTML para cada livro
                    echo '<div class="livro-resultado" style="margin-bottom: 25px; border-bottom: 1px solid #ccc; padding-bottom: 15px;">';

                    echo '<h3>' . htmlspecialchars($livro['titulo']) . '</h3>';
                    echo '<p>' . htmlspecialchars($livro['descricao']) . '</p>';
                    echo '<p><strong>Preço: R$ ' . number_format($livro['preco'], 2, ',', '.') . '</strong></p>';

                    echo '</div>';
                }
            } else {

                // 6. Mensagem de fallback (caso o banco esteja vazio)
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