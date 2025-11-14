<?php
// Ajuste o caminho do require conforme sua estrutura
require_once '../../../../The-Books-On-The-Web/public/api/conection/conectionBD.php';

if (!$con) { die("Falha na conexão: " . mysqli_connect_error()); }

$termo_pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
</head>
<body>
    <header id="header-placeholder"></header>
            
    <main>
        <div class="container-pesquisa" style="padding: 20px; max-width: 800px; margin: auto;">
            <h3>Resultado para: "<?php echo htmlspecialchars($termo_pesquisa);?>"</h3>
            <hr style="margin-bottom: 20px;">
            
            <div class="lista-livros">
                <?php
                    if (mysqli_num_rows($resultado) == 0) {
                        echo '<p style="color:#666; padding:20px; text-align:center;">Nenhum livro encontrado.</p>';
                    }
                        
                    while ($livro = mysqli_fetch_assoc($resultado)) {
                        // O caminho deve ser relativo à pasta PUBLIC (definida no <base>)
                        // Se a pasta database está em The-Books-On-The-Web/database, voltamos uma pasta (../)
                        $caminhoPdf = '/The-Books-On-The-Web/database/pdfs/' . $livro['pdf'];
                        echo '<div class="livro-card">';
                            
                            // COLUNA 1: CAPA
                            echo '<div class="capa-wrapper">';
                            if (!empty($livro['pdf'])) {
                                // Importante: data-url correto
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
                        
                        echo '</div>'; // fim card
                    }                    
                ?>
            </div>
            
            <br>
            <a href="index.php"><button type="button" class="btn-menu">Voltar</button></a>
        </div>
    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
    <script src="scripts/script.js"></script>
    <script src="scripts/pdfRender.js"></script>
</body>
</html>