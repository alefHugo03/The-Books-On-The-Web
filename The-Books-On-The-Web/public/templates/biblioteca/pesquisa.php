<?php
// Ajuste o caminho do require conforme sua estrutura
require_once (__DIR__ . '/../../api/conection/conectionBD.php');

if (!$con) { die("Falha na conexão: " . mysqli_connect_error()); }

$termo_pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : '';

// --- SQL CORRIGIDA (JOIN com tabela 'temas') ---
$sql = "SELECT livro.*, 
               GROUP_CONCAT(DISTINCT categoria.nome_categoria SEPARATOR ', ') as nome_categoria 
        FROM livro 
        -- Faz a ponte pela tabela 'temas'
        LEFT JOIN temas ON livro.id_livro = temas.fk_LIVRO_id_livro
        LEFT JOIN categoria ON temas.fk_CATEGORIA_id_categoria = categoria.id_categoria
        
        -- Filtra por Título, Descrição ou Nome da Categoria
        WHERE livro.titulo LIKE ? 
           OR livro.descricao LIKE ? 
           OR categoria.nome_categoria LIKE ?
        
        -- Agrupa para não repetir o mesmo livro se tiver várias categorias
        GROUP BY livro.id_livro";

$stmt = mysqli_prepare($con, $sql);
$termo_like = "%" . $termo_pesquisa . "%";

// Vincula os 3 parâmetros (?)
mysqli_stmt_bind_param($stmt, "sss", $termo_like, $termo_like, $termo_like);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <title>Pesquisa | TBOTW</title>
    
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    <link rel="stylesheet" href="styles/stylephone.css?v=<?php echo time(); ?>">
    
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
                        $caminhoPdf = '/The-Books-On-The-Web/database/pdfs/' . $livro['pdf'];
                        
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
                                // Exibe a categoria (ou categorias separadas por vírgula)
                                echo '<span class="categoria-tag">' . htmlspecialchars($livro['nome_categoria'] ?? 'Geral') . '</span>';
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