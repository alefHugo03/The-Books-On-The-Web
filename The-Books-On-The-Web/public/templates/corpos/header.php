<?php
session_start();
?>

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