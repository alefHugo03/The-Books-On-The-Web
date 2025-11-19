<?php
session_start();
require_once '../../api/conection/conectionBD.php'; 

// Verificação de segurança
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: ../login/entrada.html");
    exit;
}

$id_user = $_SESSION['id_user'];

// SQL CORRIGIDO: Busca Favoritos com as novas tabelas
// Usa GROUP_CONCAT para as categorias (já que agora são múltiplas)
$sql = "SELECT f.id_favorito, l.*, 
               GROUP_CONCAT(DISTINCT c.nome_categoria SEPARATOR ', ') as nome_categoria 
        FROM favoritos f
        INNER JOIN livro l ON f.id_livro = l.id_livro
        LEFT JOIN Temas t ON l.id_livro = t.fk_LIVRO_id_livro
        LEFT JOIN categoria c ON t.fk_CATEGORIA_id_categoria = c.id_categoria
        WHERE f.id_user = ?
        GROUP BY l.id_livro
        ORDER BY f.data_favoritado DESC";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon" class="favicon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js"></script>
    <title>Meus Favoritos | TBOTW</title>
</head>

<body>
    <header id="header-placeholder"></header>

    <main>
        <div class="container-vitrine" style="padding: 20px; max-width: 1000px; margin: auto; width: 100%;">
            <h2>Meus Livros Favoritos</h2>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #ddd;">

            <div class="lista-livros">
                <?php
                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    while ($livro = mysqli_fetch_assoc($resultado)) {
                        // CAMINHO ABSOLUTO PADRONIZADO
                        $caminhoPdf = '/The-Books-On-The-Web/database/pdfs/' . $livro['pdf'];
                        
                        // Tag de categoria (pega a primeira se tiver várias ou exibe 'Geral')
                        $cats = !empty($livro['nome_categoria']) ? explode(',', $livro['nome_categoria'])[0] : 'Geral';
                        
                        echo '<a href="templates/biblioteca/livros.php?id=' . $livro['id_livro'] . '" style="text-decoration:none; color:inherit;">';
                        echo '<div class="livro-card">';

                        // CAPA
                        echo '<div class="capa-wrapper">';
                        if (!empty($livro['pdf'])) {
                            // O canvas precisa do caminho correto para renderizar
                            echo '<canvas class="pdf-thumb" data-url="' . $caminhoPdf . '"></canvas>';
                        } else {
                            echo '<div class="sem-capa">Sem Capa</div>';
                        }
                        echo '</div>';

                        // TEXTO
                        echo '<div class="info-livro">';
                        echo '<h3>' . htmlspecialchars($livro['titulo']) . '</h3>';
                        echo '<p>' . htmlspecialchars($livro['descricao']) . '</p>';
                        echo '<span class="categoria-tag">' . htmlspecialchars($cats) . '</span>';
                        echo '</div>';

                        echo '</div>'; // Fim card
                        echo '</a>';
                    }
                } else {
                    echo '<div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #666;">';
                    echo '<p>Você ainda não favoritou nenhum livro.</p>';
                    echo '<a href="index.php" style="color:var(--cor-destaque); font-weight:bold;">Explorar biblioteca</a>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
</body>
<script src="scripts/script.js"></script>
<script src="scripts/pdfRender.js"></script>

</html>