<?php 
session_start();
require_once '../../api/conection/conectionBD.php';

// 1. Recupera o ID do livro (Segurança contra injeção)
$id_livro = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_livro === 0) {
    header("Location: ../../index.php");
    exit;
}

$id_user = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
$tipo_user = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'visitante';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_favorito') {
    if ($id_livro === 0 || $id_user === 0 || $tipo_user === 'admin') {
        header("Location: livros.php?id=$id_livro");
        exit;
    }
    $check = mysqli_query($con, "SELECT id_favorito FROM favoritos WHERE id_user = $id_user AND id_livro = $id_livro");
    if ($check && mysqli_num_rows($check) > 0) {
        mysqli_query($con, "DELETE FROM favoritos WHERE id_user = $id_user AND id_livro = $id_livro");
    } else {
        mysqli_query($con, "INSERT INTO favoritos (id_user, id_livro) VALUES ($id_user, $id_livro)");
    }
    // Recarrega a página para atualizar o ícone
    header("Location: livros.php?id=$id_livro");
    exit;
}

$sql = "SELECT l.*, c.nome_categoria, a.nome_autor FROM livro l LEFT JOIN categoria c ON l.categoria = c.id_categoria LEFT JOIN escritor e ON l.id_livro = e.livro LEFT JOIN autor a ON e.autor = a.id_autor WHERE l.id_livro = $id_livro";
$res = mysqli_query($con, $sql);
$livro = mysqli_fetch_assoc($res);

if (!$livro) { 
    // Se o livro não existe, volta para home
    header("Location: ../../index.php"); 
    exit; 
}

$favCheck = mysqli_query($con, "SELECT id_favorito FROM favoritos WHERE id_user = $id_user AND id_livro = $id_livro");
$isFavorito = ($favCheck && mysqli_num_rows($favCheck) > 0);
$caminhoPdf = '/The-Books-On-The-Web/database/pdfs/' . $livro['pdf'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://192.168.0.136:80/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($livro['titulo']); ?> | Detalhes</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    <link rel="stylesheet" href="styles/stylephone.css?v=<?php echo time(); ?>">
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
                <?php if(!empty($livro['nome_autor'])): ?>
                    <h3 style="color:#555; margin-bottom:10px; font-weight:normal;">Por: <strong><?php echo htmlspecialchars($livro['nome_autor']); ?></strong></h3>
                <?php endif; ?>
                <p class="detalhes-meta"><strong>Data de Publicação:</strong> <?php echo date('d/m/Y', strtotime($livro['data_publi'])); ?></p>
                <div class="sinopse-box">
                    <h3>Sinopse</h3>
                    <p class="texto-sinopse"><?php echo nl2br(htmlspecialchars($livro['descricao'])); ?></p>
                </div>
                <div class="grupo-botoes">
                    <a href="<?php echo $caminhoPdf; ?>" target="_blank" class="btn-acao btn-ler">Ler / Baixar PDF</a>
                    <?php if ($tipo_user !== 'admin'): ?>
                        <form method="POST" action="" style="display:flex;">
                            <input type="hidden" name="action" value="toggle_favorito">
                            <?php if ($isFavorito): ?>
                                <button type="submit" class="btn-acao btn-fav-remover">
                                    <svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg> Favoritado
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn-acao btn-fav-adicionar">
                                    <svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg> Favoritar
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