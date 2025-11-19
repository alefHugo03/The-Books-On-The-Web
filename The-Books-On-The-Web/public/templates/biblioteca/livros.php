<?php 
session_start();
// Ajuste o caminho conforme sua estrutura de pastas
require_once '../../api/conection/conectionBD.php';

// 1. Recupera o ID do livro (Segurança contra injeção)
$id_livro = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_livro === 0) {
    header("Location: ../../index.php");
    exit;
}

$id_user = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
$tipo_user = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'visitante';

// 2. Lógica de Favoritar (Processada antes de carregar a página)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_favorito') {
    // Apenas clientes logados podem favoritar
    if ($id_user > 0 && $tipo_user === 'cliente') {
        $check = mysqli_query($con, "SELECT id_favorito FROM favoritos WHERE id_user = $id_user AND id_livro = $id_livro");
        if ($check && mysqli_num_rows($check) > 0) {
            mysqli_query($con, "DELETE FROM favoritos WHERE id_user = $id_user AND id_livro = $id_livro");
        } else {
            mysqli_query($con, "INSERT INTO favoritos (id_user, id_livro) VALUES ($id_user, $id_livro)");
        }
    }
    // Recarrega a página para atualizar o ícone
    header("Location: livros.php?id=$id_livro");
    exit;
}

// 3. BUSCAR DETALHES (SQL ATUALIZADO PARA N:N)
// Traz Editora (1:N), Autores (N:N) e Categorias (N:N)
$sql = "SELECT l.*, 
               ed.nome_editora,
               GROUP_CONCAT(DISTINCT c.nome_categoria SEPARATOR ', ') as nome_categoria,
               GROUP_CONCAT(DISTINCT a.nome_autor SEPARATOR ', ') as nome_autor
        FROM livro l 
        LEFT JOIN editora ed ON l.fk_editora = ed.id_editora
        LEFT JOIN Temas t ON l.id_livro = t.fk_LIVRO_id_livro
        LEFT JOIN categoria c ON t.fk_CATEGORIA_id_categoria = c.id_categoria
        LEFT JOIN ESCRITOR e ON l.id_livro = e.FK_LIVRO_id_livro
        LEFT JOIN autor a ON e.FK_AUTOR_id_autor = a.id_autor
        WHERE l.id_livro = ?
        GROUP BY l.id_livro";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_livro);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$livro = mysqli_fetch_assoc($res);

if (!$livro) { 
    // Se o livro não existe, volta para home
    header("Location: ../../index.php"); 
    exit; 
}

// 4. Checar se é favorito (para pintar o botão)
$isFavorito = false;
if ($id_user > 0) {
    $favCheck = mysqli_query($con, "SELECT id_favorito FROM favoritos WHERE id_user = $id_user AND id_livro = $id_livro");
    $isFavorito = ($favCheck && mysqli_num_rows($favCheck) > 0);
}

// 5. Caminho Padronizado do PDF
$caminhoPdf = '/The-Books-On-The-Web/database/pdfs/' . $livro['pdf'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($livro['titulo']); ?> | Detalhes</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.worker.min.js"></script>
</head>
<body>
    <header id="header-placeholder"></header>
            
    <main>
        <div class="container-voltar">
            <a href="index.php" class="btn-voltar">⬅ Voltar</a>
        </div>

        <div class="container-detalhes">
            <div class="detalhes-capa">
                <?php if (!empty($livro['pdf'])): ?>
                    <canvas class="pdf-thumb" data-url="<?php echo $caminhoPdf; ?>"></canvas>
                <?php else: ?>
                    <div class="sem-capa">Sem Capa Disponível</div>
                <?php endif; ?>
                
                <?php if(!empty($livro['nome_categoria'])): ?>
                    <div style="margin-top:10px; text-align:center;">
                        <?php 
                            $cats = explode(',', $livro['nome_categoria']);
                            foreach($cats as $cat) {
                                echo '<span class="categoria-badge" style="margin:2px;">'.trim($cat).'</span> ';
                            }
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="detalhes-info">
                <h1><?php echo htmlspecialchars($livro['titulo']); ?></h1>
                
                <div class="detalhes-meta">
                    <p><strong>Autor(es):</strong> <?php echo htmlspecialchars($livro['nome_autor'] ?? 'Desconhecido'); ?></p>
                    <p><strong>Editora:</strong> <?php echo htmlspecialchars($livro['nome_editora'] ?? 'Não informada'); ?></p>
                    <p><strong>Data de Publicação:</strong> <?php echo date('d/m/Y', strtotime($livro['data_publi'])); ?></p>
                </div>

                <div class="sinopse-box">
                    <h3>Sinopse</h3>
                    <p class="texto-sinopse">
                        <?php echo nl2br(htmlspecialchars($livro['descricao'])); ?>
                    </p>
                </div>

                <div class="grupo-botoes">
                    <a href="<?php echo $caminhoPdf; ?>" target="_blank" class="btn-acao btn-ler">
                        <svg class="icon-svg" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        Ler / Baixar PDF
                    </a>

                    <?php if ($id_user > 0 && $tipo_user === 'cliente'): ?>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="action" value="toggle_favorito">
                            
                            <?php if ($isFavorito): ?>
                                <button type="submit" class="btn-acao btn-fav-remover" title="Remover dos favoritos">
                                    <svg class="icon-svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                    Remover dos Favoritos
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn-acao btn-fav-adicionar" title="Adicionar aos favoritos">
                                    <svg class="icon-svg" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                    Adicionar aos Favoritos
                                </button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
    <script src="scripts/script.js"></script>
    <script src="scripts/pdfRender.js"></script>
</body>
</html>