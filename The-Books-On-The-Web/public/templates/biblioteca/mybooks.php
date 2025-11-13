<!-- <?php 
session_start();


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg"  type="image/x-icon" class="favicon">
    <title>Meus Livros | TBOTW</title>
</head>
<body>
    <header id="header-placeholder"></header>
            
    <main>

    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
</body>
<script src="scripts/script.js"></script>
</html> -->

<?php
require_once '../../api/conection/conectionBD.php';

// Verificar se está logado e é admin
if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: http://localhost/The-Books-On-The-Web/public/templates/login/entrada.html");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete':
                if (isset($_POST['livro_id'])) {
                    $livro_id = (int)$_POST['livro_id'];

                    $q = $con->prepare("SELECT pdf FROM livro WHERE id_livro = ?");
                    $q->bind_param("i", $livro_id);
                    $q->execute();
                    $res = $q->get_result()->fetch_assoc();
                    if ($res && !empty($res['pdf'])) {
                        $path = __DIR__ . "/../uploads/pdfs/" . $res['pdf'];
                        if (file_exists($path)) unlink($path);
                    }

                    $stmt = $con->prepare("DELETE FROM livro WHERE id_livro = ?");
                    $stmt->bind_param("i", $livro_id);

                    if ($stmt->execute()) {
                        $message = "Livro excluído com sucesso!";
                    } else {
                        $message = "Erro ao excluir livro: " . $con->error;
                    }
                }
                break;

            case 'add_categoria':
                $nome_categoria = trim($_POST['nome_categoria']);
                if (!empty($nome_categoria)) {
                    $stmt = $con->prepare("INSERT INTO categoria (nome_categoria) VALUES (?)");
                    $stmt->bind_param("s", $nome_categoria);
                    if ($stmt->execute()) {
                        $new_cat_id = $con->insert_id;
                        echo json_encode(['success' => true, 'id' => $new_cat_id, 'nome' => $nome_categoria]);
                        exit;
                    } else {
                        echo json_encode(['success' => false, 'error' => $con->error]);
                        exit;
                    }
                }
                break;

            case 'add':
            case 'edit':
                $titulo = trim($_POST['titulo']);
                $descricao = trim($_POST['descricao']);
                $data_publi = $_POST['data_publi'];
                $categoria = (int)$_POST['categoria'];

                // Validação: categoria obrigatória
                if ($categoria <= 0) {
                    $message = 'Por favor, selecione uma categoria.';
                    break;
                }

                // Simples: tratar upload de PDF se presente
                $pdf_filename = null;
                if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $file = $_FILES['pdf_file'];
                    if ($file['error'] !== UPLOAD_ERR_OK) { $message = 'Erro no upload do arquivo.'; break; }

                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if ($ext !== 'pdf') { $message = 'Arquivo inválido. Envie apenas PDF.'; break; }

                    $maxSize = 20 * 1024 * 1024; // 20MB
                    if ($file['size'] > $maxSize) { $message = 'Arquivo muito grande. Limite: 20MB.'; break; }

                    $safeName = uniqid('book_', true) . '.' . $ext;
                    $destDir = __DIR__ . '/../uploads/pdfs/';
                    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
                    if (!move_uploaded_file($file['tmp_name'], $destDir . $safeName)) { $message = 'Erro ao mover o arquivo.'; break; }

                    $pdf_filename = $safeName;
                }

                if ($_POST['action'] === 'add') {
                    if ($pdf_filename !== null) {
                        $stmt = $con->prepare("INSERT INTO livro (titulo, descricao, data_publi, categoria, pdf) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssdsis", $titulo, $descricao, $data_publi, $categoria, $pdf_filename);
                    } else {
                        $stmt = $con->prepare("INSERT INTO livro (titulo, descricao, data_publi, categoria) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssdsi", $titulo, $descricao, $data_publi, $categoria);
                    }
                } else {
                    $livro_id = (int)$_POST['livro_id'];

                    if ($pdf_filename !== null) {
                        // remover PDF antigo, se existir
                        $q = $con->prepare("SELECT pdf FROM livro WHERE id_livro = ?");
                        $q->bind_param("i", $livro_id);
                        $q->execute();
                        $old = $q->get_result()->fetch_assoc();
                        if ($old && !empty($old['pdf'])) {
                            $oldPath = __DIR__ . "/../uploads/pdfs/" . $old['pdf'];
                            if (file_exists($oldPath)) unlink($oldPath);
                        }

                        $stmt = $con->prepare("UPDATE livro SET titulo = ?, descricao = ?, data_publi = ?, categoria = ?, pdf = ? WHERE id_livro = ?");
                        $stmt->bind_param("ssdsisi", $titulo, $descricao, $data_publi, $categoria, $pdf_filename, $livro_id);
                    } else {
                        $stmt = $con->prepare("UPDATE livro SET titulo = ?, descricao = ?, data_publi = ?, categoria = ? WHERE id_livro = ?");
                        $stmt->bind_param("ssdsii", $titulo, $descricao, $data_publi, $categoria, $livro_id);
                    }
                }

                if ($stmt->execute()) {
                    $message = "Livro " . ($_POST['action'] === 'add' ? "adicionado" : "atualizado") . " com sucesso!";
                } else {
                    $message = "Erro ao " . ($_POST['action'] === 'add' ? "adicionar" : "atualizar") . " livro: " . $con->error;
                }
                break;
        }
    }
}

// Buscar categorias para o formulário
$categorias = [];
$result = mysqli_query($con, "SELECT * FROM categoria ORDER BY nome_categoria");
while ($row = mysqli_fetch_assoc($result)) {
    $categorias[] = $row;
}

// Buscar todos os livros
$livros = [];
$sql = "SELECT l.*, c.nome_categoria 
        FROM livro l 
        LEFT JOIN categoria c ON l.categoria = c.id_categoria 
        ORDER BY l.titulo";
$result = mysqli_query($con, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $livros[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg"  type="image/x-icon" class="favicon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros - Painel Administrativo</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header id="header-placeholder"></header>

    <main class="container">
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <section class="admin-content">
            <button onclick="showForm()" class="btn-menu">Adicionar Novo Livro</button>

            <div id="livroForm" class="caixa-menu cadastro-menu" style="display: none;">
                <h3>Adicionar/Editar Livro</h3>
                <form id="livroFormElement" method="POST" action="mybooks.php" enctype="multipart/form-data">
                    <input type="hidden" id="action" name="action" value="add" class="valor-texto">
                    <input type="hidden" id="livro_id" name="livro_id" value="" class="valor-texto">

                    <div class="valor caixa-texto">
                        <label for="titulo">Título:</label>
                        <input type="text" id="titulo" name="titulo" class="valor-texto" required>
                    </div>

                    <div class="valor caixa-texto">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" class="valor-texto"></textarea>
                    </div>

                    <div class="valor caixa-texto">
                        <label for="data_publi">Data de Publicação:</label>
                        <input type="date" id="data_publi" name="data_publi" class="valor-texto">
                    </div>

                    <div class="valor caixa-texto">
                        <label for="categoria">Categoria:</label>
                        <select id="categoria" name="categoria" class="valor-texto" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id_categoria']; ?>">
                                    <?php echo htmlspecialchars($cat['nome_categoria']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="showNewCategoryForm()" class="btn-menu">Nova Categoria</button>
                    </div>

                    <div class="valor caixa-texto">
                        <label for="pdf_file">Arquivo PDF (opcional):</label>
                        <input type="file" id="pdf_file" name="pdf_file" accept="application/pdf" class="valor-texto">
                        <div><small id="existingPdf" class="text-muted"></small></div>
                    </div>

                    <button type="submit" class="btn-menu">Salvar</button>
                    <button type="button" onclick="hideForm()" class="btn-menu">Cancelar</button>
                </form>

                <!-- Modal para nova categoria (fora do form para não bloquear validação) -->
                <div id="novaCategoriaModal" class="modal">
                    <div class="modal-content">
                        <h4>Nova Categoria</h4>
                        <div class="form-group">
                            <label for="nome_categoria">Nome da Categoria:</label>
                            <input type="text" id="nome_categoria" name="nome_categoria" class="valor-texto">
                        </div>
                        <button type="button" onclick="salvarCategoria()" class="btn-menu">Salvar</button>
                        <button type="button" onclick="hideNewCategoryForm()" class="btn-menu">Cancelar</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descrição</th>
                            <th>Data Publicação</th>
                            <th>Categoria</th>
                            <th>Arquivo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($livro['descricao']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($livro['data_publi'])); ?></td>
                                <td><?php echo htmlspecialchars($livro['nome_categoria']); ?></td>
                                        <td>
                                            <?php if (!empty($livro['pdf'])): ?>
                                                <a href="../uploads/pdfs/<?php echo urlencode($livro['pdf']); ?>" target="_blank">Baixar PDF</a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                    <button onclick="editarLivro(<?php echo htmlspecialchars(json_encode($livro)); ?>)" class="btn-small">Editar</button>
                                    <form method="POST" action="livros.php" style="display: inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="livro_id" value="<?php echo $livro['id_livro']; ?>">
                                        <button type="submit" class="btn-menu" onclick="return confirm('Tem certeza que deseja excluir este livro?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="main-footer" id="footer-placeholder"></footer>

    <script>
    function showForm() {
        document.getElementById('livroForm').style.display = 'block';
        document.getElementById('action').value = 'add';
        document.getElementById('livro_id').value = '';
        // Limpar formulário
        document.getElementById('titulo').value = '';
        document.getElementById('descricao').value = '';
        document.getElementById('data_publi').value = '';
        document.getElementById('categoria').value = '';
        document.getElementById('existingPdf').innerText = '';
    }

    function hideForm() {
        document.getElementById('livroForm').style.display = 'none';
    }

    // O formulário será enviado normalmente (sem interceptação JS)

    function editarLivro(livro) {
        document.getElementById('livroForm').style.display = 'block';
        document.getElementById('action').value = 'edit';
        document.getElementById('livro_id').value = livro.id_livro;
        document.getElementById('titulo').value = livro.titulo;
        document.getElementById('descricao').value = livro.descricao;
        document.getElementById('data_publi').value = livro.data_publi;
        document.getElementById('categoria').value = livro.categoria;
        document.getElementById('existingPdf').innerText = livro.pdf ? ('Arquivo atual: ' + livro.pdf) : '';
    }

    // Preencher título automaticamente ao selecionar um PDF (se o título estiver vazio)
    (function() {
        var pdfInput = document.getElementById('pdf_file');
        if (!pdfInput) return;
        pdfInput.addEventListener('change', function() {
            var f = this.files && this.files[0];
            if (!f) return;
            var name = f.name.replace(/\.[^/.]+$/, ''); // remove extensão
            var titleEl = document.getElementById('titulo');
            if (titleEl && titleEl.value.trim() === '') {
                titleEl.value = name;
            }
        });
    })();

    function showNewCategoryForm() {
        document.getElementById('novaCategoriaModal').style.display = 'block';
    }

    function hideNewCategoryForm() {
        document.getElementById('novaCategoriaModal').style.display = 'none';
    }

    function salvarCategoria() {
        const nome = document.getElementById('nome_categoria').value.trim();
        if (!nome) {
            alert('Por favor, insira o nome da categoria.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'add_categoria');
        formData.append('nome_categoria', nome);

        fetch('livros.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Adicionar nova categoria ao select
                const select = document.getElementById('categoria');
                const option = document.createElement('option');
                option.value = data.id;
                option.textContent = data.nome;
                select.appendChild(option);
                
                // Selecionar a nova categoria
                select.value = data.id;
                
                // Fechar modal e limpar campo
                hideNewCategoryForm();
                document.getElementById('nome_categoria').value = '';
            } else {
                alert('Erro ao criar categoria: ' + (data.error || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao criar categoria. Por favor, tente novamente.');
        });
    }

    // Fechar modal ao clicar fora dele
    window.onclick = function(event) {
        const modal = document.getElementById('novaCategoriaModal');
        if (event.target == modal) {
            hideNewCategoryForm();
        }
    }
    </script>
</body>
<script src="scripts/script.js"></script>
</html>