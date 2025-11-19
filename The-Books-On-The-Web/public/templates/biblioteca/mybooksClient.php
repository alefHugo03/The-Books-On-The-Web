<?php
session_start();
require_once (__DIR__ . '/../../api/conection/conectionBD.php'); 
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'cliente') {
    header("Location: http://192.168.0.136:80/The-Books-On-The-Web/public/templates/login/entrada.html");
    exit;
}
$id_user = $_SESSION['id_user'];
$sql = "SELECT l.*, c.nome_categoria FROM livro l INNER JOIN favoritos f ON l.id_livro = f.id_livro LEFT JOIN categoria c ON l.categoria = c.id_categoria WHERE f.id_user = $id_user ORDER BY f.data_favoritado DESC";
$resultado = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://192.168.0.136:80/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    <link rel="stylesheet" href="styles/stylephone.css?v=<?php echo time(); ?>">
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
                        $caminhoPdf = '../../../database/pdfs/' . $livro['pdf'];
                        echo '<a href="templates/biblioteca/livros.php?id=' . $livro['id_livro'] . '" style="text-decoration:none; color:inherit;">';
                        echo '<div class="livro-card">';
                        echo '<div class="capa-wrapper">';
                        if (!empty($livro['pdf'])) { echo '<canvas class="pdf-thumb" data-url="' . $caminhoPdf . '"></canvas>'; } else { echo '<div class="sem-capa">Sem Capa</div>'; }
                        echo '</div>';
                        echo '<div class="info-livro">';
                        echo '<h3>' . htmlspecialchars($livro['titulo']) . '</h3>';
                        echo '<p>' . htmlspecialchars($livro['descricao']) . '</p>';
                        echo '<span class="categoria-tag">' . htmlspecialchars($cats) . '</span>';
                        echo '</div>';
                        echo '</div>';
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