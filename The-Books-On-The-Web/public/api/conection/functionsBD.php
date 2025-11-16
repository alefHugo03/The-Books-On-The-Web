<?php
function procurarLivros() {
    global $con;
    $sql = "SELECT livro.*, categoria.nome_categoria 
        FROM livro 
        INNER JOIN categoria ON livro.categoria = categoria.id_categoria
    ORDER BY RAND() LIMIT 6";
    $resultado = mysqli_query($con, $sql);

    if ($resultado === false) {
        die("ERRO NO SQL: " . mysqli_error($con));
    }

    return $resultado;
}
// ifIsAdmin() {
//     if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') {
//     }
// }
// ifIsClient() {
//         if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'cliente') {
//         }
// } 

function exibirMenuAutenticacao() {
    if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
        echo '<div class="perfil-header">
            <span class="saudacao">Olá, ' . htmlspecialchars($_SESSION['nome_user']) . '!</span>
                <a href="templates/login/painel_logado.php" class="btn-header">Perfil</a>
                <a href="api/login/logout.php" class="btn-header">Sair</a>
            </div>';

    } else {
        echo "<div class='perfil-header'>";
        echo '<a href="templates/login/entrada.html" class="btn-cadastro">Entrar</a>';
        echo '<a href="templates/login/cadastro.html" class="btn-cadastro">Cadastrar-se</a>';
        echo "</div>";
    }
}

function exibirBotoesCliente(){
    if (isset($_SESSION['logado']) && $_SESSION['tipo'] === 'cliente') {
        echo '<a href="templates/biblioteca/mybooksClient.php" class="item-menu">Meus Livros</a>';
    }
}

function exibirBotoesAdimin(){
    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') {
        echo '<a href="templates/biblioteca/admin/mybooksAdmin.php" class="item-menu">Meus Livros</a>
         <a href="templates/biblioteca/admin/painel_admin.php" class="item-menu">Painel Admin</a>';
    }
}

?>