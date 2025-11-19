<?php
// ARQUIVO: public/api/conection/functionsBD.php

// Função ajustada para a nova estrutura N:N
function procurarLivros($inicio = 0, $quantidade = 6) {
    global $con;
    
    // SQL atualizado: Faz JOIN com Temas e CATEGORIA, e agrupa os nomes
    $sql = "SELECT l.*, 
                   GROUP_CONCAT(DISTINCT c.nome_categoria SEPARATOR ', ') as nome_categoria 
            FROM livro l 
            LEFT JOIN Temas t ON l.id_livro = t.fk_LIVRO_id_livro
            LEFT JOIN categoria c ON t.fk_CATEGORIA_id_categoria = c.id_categoria
            GROUP BY l.id_livro
            ORDER BY l.id_livro DESC 
            LIMIT $inicio, $quantidade";
            
    $resultado = mysqli_query($con, $sql);

    if ($resultado === false) {
        die("ERRO NO SQL: " . mysqli_error($con));
    }

    return $resultado;
}

// Função para contar total (Não mudou, mas mantemos aqui)
function contarTotalLivros() {
    global $con;
    $sql = "SELECT COUNT(*) as total FROM livro";
    $resultado = mysqli_query($con, $sql);
    $dados = mysqli_fetch_assoc($resultado);
    return $dados['total'];
}

// --- Funções de menu (Mantidas) ---

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