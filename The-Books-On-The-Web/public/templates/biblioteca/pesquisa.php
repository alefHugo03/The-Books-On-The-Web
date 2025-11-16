<?php
// Ajuste o caminho do require conforme sua estrutura
require_once '../../../../The-Books-On-The-Web/public/api/conection/conectionBD.php';

if (!$con) { die("Falha na conexão: " . mysqli_connect_error()); }

$termo_pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// Query ajustada para buscar também informações úteis se precisar
$sql = "SELECT livro.*, categoria.nome_categoria 
        FROM livro 
        INNER JOIN categoria ON livro.categoria = categoria.id_categoria
        WHERE livro.titulo LIKE ? OR livro.descricao LIKE ? OR categoria.nome_categoria LIKE ?";

$stmt = mysqli_prepare($con, $sql);
$termo_like = "%" . $termo_pesquisa . "%";
mysqli_stmt_bind_param($stmt, "sss", $termo_like, $termo_like, $termo_like);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <title>Pesquisa | TBOTW</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
</head>
<body>
    <header id="header-placeholder"></header>
            
    <main>
        <div class="container-pesquisa" style="padding: 20px; max-width: 1000px; margin: auto;">
            <h2>Resultado para: "<?php echo htmlspecialchars($termo_pesquisa);?>"</h2>
            <hr style="margin-bottom: 20px;">
            
            <div class="lista-livros">
                <?php
                    if (mysqli_num_rows($resultado) == 0) {
                        echo '<p style="color:#666; padding:20px; text-align:center; width:100%;">Nenhum livro encontrado.</p>';
                    }
                        
                    while ($livro = mysqli_fetch_assoc($resultado)) {
                        // Caminho absoluto para o PDF (para o renderizador JS)
                        $caminhoPdf = '/The-Books-On-The-Web/database/pdfs/' . $livro['pdf'];
                        
                        // -- ALTERAÇÃO AQUI: Link envolvendo o card --
                        // Aponta para templates/biblioteca/livros.php enviando o ID
                        echo '<a href="templates/biblioteca/livros.php?id=' . $livro['id_livro'] . '" style="text-decoration:none; color:inherit;">';
                        
                        echo '<div class="livro-card">';
                            
                            // COLUNA 1: CAPA
                            echo '<div class="capa-wrapper">';
                            if (!empty($livro['pdf'])) {
                                echo '<canvas class="pdf-thumb" data-url="' . $caminhoPdf . '"></canvas>';
                            } else {
                                echo '<div class="sem-capa">Sem Capa</div>';
                            }
                            echo '</div>';

                            // COLUNA 2: INFORMAÇÕES
                            echo '<div class="info-livro">';
                                echo '<h3>' . htmlspecialchars($livro['titulo']) . '</h3>';
                                echo '<p>' . htmlspecialchars($livro['descricao']) . '</p>';
                                echo '<span class="categoria-tag">' . htmlspecialchars($livro['nome_categoria']) . '</span>';
                            echo '</div>';
                        
                        echo '</div>'; // fim div card
                        echo '</a>';   // fim link a
                    }                    
                ?>
            </div>
            
            <br>
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php" class="btn-menu" style="padding: 10px 20px; background-color: #333; color: white; border-radius: 5px;">Voltar para Home</a>
            </div>
        </div>
    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
    <script src="scripts/script.js"></script>
    <script src="scripts/pdfRender.js"></script>
</body>
</html>