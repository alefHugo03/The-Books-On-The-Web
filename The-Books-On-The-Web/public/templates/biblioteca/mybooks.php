<?php
session_start();
require_once '../../api/conection/conectionBD.php';

// Verificar se está logado e é admin
if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: http://localhost/The-Books-On-The-Web/public/templates/login/entrada.html");
    exit();
}

$message = '';
$msgType = ''; // 'success' ou 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lógica de POST (mantida igual, só adicionei controle de tipo de mensagem)
    if (isset($_POST['action'])) {
        // ... (Sua lógica de 'delete', 'add_categoria' continua igual aqui) ...
        // ... (Vou resumir para focar na estrutura, mantenha seu código PHP de lógica aqui) ...
        
        switch ($_POST['action']) {
            case 'delete':
                // ... (seu código de delete) ...
                // Se sucesso:
                $message = "Livro excluído com sucesso!";
                $msgType = 'success';
                break;

            case 'add_categoria':
                // (Este bloco geralmente responde JSON e sai, então não afeta o HTML abaixo)
                // ... (seu código de add_categoria) ...
                break;

            case 'add':
            case 'edit':
                // ... (seu código de upload e insert/update) ...
                // Ao final:
                if (isset($stmt) && $stmt->execute()) {
                    $message = "Livro " . ($_POST['action'] === 'add' ? "adicionado" : "atualizado") . " com sucesso!";
                    $msgType = 'success';
                } else {
                    $message = "Erro: " . $con->error; // Ou mensagem do upload
                    $msgType = 'error';
                }
                break;
        }
    }
}

// Buscar categorias
$categorias = [];
$result = mysqli_query($con, "SELECT * FROM categoria ORDER BY nome_categoria");
while ($row = mysqli_fetch_assoc($result)) {
    $categorias[] = $row;
}

// Buscar livros
$livros = [];
$sql = "SELECT l.*, c.nome_categoria FROM livro l LEFT JOIN categoria c ON l.categoria = c.id_categoria ORDER BY l.titulo";
$result = mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $livros[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros - Painel Administrativo</title>
    
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/livros.css">
    
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon" class="favicon">
</head>
<body>
    <header id="header-placeholder"></header>

    <main class="painel-admin">
        
        <?php if (!empty($message)): ?>
            <div class="feedback <?php echo $msgType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h2>
            Adicionar/Editar Livro
            <button id="btn-toggle-cadastro" type="button" title="Alternar Formulário">+</button>
        </h2>

        <form class="form-create menu conteudo-oculto" id="form-cadastro" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="livro_id" id="livro_id" value="">

            <div class="valor caixa-texto">
                <label for="titulo">Título do Livro:</label>
                <input type="text" name="titulo" id="titulo" class="valor-texto" required placeholder="Ex: Dom Casmurro">
            </div>

            <div class="valor caixa-texto">
                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao" class="valor-texto" rows="3" placeholder="Sinopse do livro..."></textarea>
            </div>

            <div class="valor caixa-texto">
                <label for="data_publi">Data de Publicação:</label>
                <input type="date" name="data_publi" id="data_publi" class="valor-texto" required>
            </div>

            <div class="valor caixa-texto">
                <label for="categoria">Categoria:</label>
                <div style="display:flex; gap:10px;">
                    <select name="categoria" id="categoria" class="valor-texto" style="flex-grow:1;" required>
                        <option value="" disabled selected>Selecione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>">
                                <?php echo htmlspecialchars($cat['nome_categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="showNewCategoryForm()" class="btn-small" style="background:#17a2b8; color:white;">+ Nova</button>
                </div>
            </div>

            <div class="valor caixa-texto">
                <label for="pdf_file">Arquivo PDF:</label>
                <input type="file" name="pdf_file" id="pdf_file" class="valor-texto" accept=".pdf">
                <small id="existingPdf" style="color:#666; margin-top:5px; font-style:italic;"></small>
            </div>

            <div class="interativo">
                <button type="submit" id="btn-menu-criar">Salvar Livro</button>
            </div>
        </form>

        <hr style="margin: 30px 0; border:0; border-top:1px solid #eee;">

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Data Pub.</th>
                        <th>Arquivo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($livros)): ?>
                        <tr><td colspan="5" style="text-align:center;">Nenhum livro cadastrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($livro['nome_categoria']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($livro['data_publi'])); ?></td>
                                <td>
                                    <?php if (!empty($livro['pdf'])): ?>
                                        <a href="../uploads/pdfs/<?php echo urlencode($livro['pdf']); ?>" target="_blank" style="color:#007bff; text-decoration:none;">Ver PDF</a>
                                    <?php else: ?>
                                        <span style="color:#999;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" onclick='editarLivro(<?php echo json_encode($livro); ?>)' class="btn-small">Editar</button>
                                    
                                    <form method="POST" action="livros.php" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este livro?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="livro_id" value="<?php echo $livro['id_livro']; ?>">
                                        <button type="submit" class="btn-delete">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="novaCategoriaModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="hideNewCategoryForm()">&times;</span>
                <h3>Nova Categoria</h3>
                <div class="valor caixa-texto">
                    <label for="nome_categoria_modal">Nome:</label>
                    <input type="text" id="nome_categoria_modal" class="valor-texto">
                </div>
                <div style="margin-top:15px; text-align:right;">
                    <button type="button" onclick="salvarCategoria()" id="btn-menu-criar" style="width:auto;">Salvar</button>
                </div>
            </div>
        </div>

    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>

     
</body>
<script src="scripts/script.js"></script> <script src="scripts/livros.js"></script>
<script type="module" src="scripts/biblioteca/livros.js"></script>
</html>