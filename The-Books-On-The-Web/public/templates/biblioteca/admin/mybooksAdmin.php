<?php
session_start();

// Ajuste este caminho conforme sua estrutura de pastas real
require_once '../../../api/conection/conectionBD.php';

// 1. Segurança: Apenas Admin
if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../login/painel_logado.php");
    exit();
}

// --- Lógica de Feedback (Mensagens) ---
$message = '';
$msgType = '';
if (isset($_SESSION['feedback_msg'])) {
    $message = $_SESSION['feedback_msg'];
    $msgType = $_SESSION['feedback_type'];
    unset($_SESSION['feedback_msg']);
    unset($_SESSION['feedback_type']);
}

// --- PROCESSAMENTO DO POST ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // A. AJAX: ADICIONAR CATEGORIA
    if (isset($_POST['action']) && $_POST['action'] === 'add_categoria') {
        header('Content-Type: application/json');
        $nome = trim($_POST['nome_categoria']);
        if (empty($nome)) { echo json_encode(['success'=>false, 'error'=>'Nome vazio']); exit; }
        
        $stmt = $con->prepare("INSERT INTO categoria (nome_categoria) VALUES (?)");
        $stmt->bind_param("s", $nome);
        echo $stmt->execute() ? json_encode(['success'=>true]) : json_encode(['success'=>false, 'error'=>$con->error]);
        exit; 
    }

    // B. AJAX: ADICIONAR AUTOR
    if (isset($_POST['action']) && $_POST['action'] === 'add_autor') {
        header('Content-Type: application/json');
        $nome = trim($_POST['nome_autor']);
        if (empty($nome)) { echo json_encode(['success'=>false, 'error'=>'Nome vazio']); exit; }

        $stmt = $con->prepare("INSERT INTO autor (nome_autor) VALUES (?)");
        $stmt->bind_param("s", $nome);
        echo $stmt->execute() ? json_encode(['success'=>true]) : json_encode(['success'=>false, 'error'=>$con->error]);
        exit; 
    }

    // C. AJAX: EXCLUIR AUTOR
    if (isset($_POST['action']) && $_POST['action'] === 'delete_autor') {
        header('Content-Type: application/json');
        $id = $_POST['id_autor'];
        // Remove vínculos primeiro
        $con->query("DELETE FROM escritor WHERE autor = $id");
        
        $stmt = $con->prepare("DELETE FROM autor WHERE id_autor = ?");
        $stmt->bind_param("i", $id);
        echo $stmt->execute() ? json_encode(['success'=>true]) : json_encode(['success'=>false, 'error'=>$con->error]);
        exit;
    }

    // D. AJAX: EXCLUIR CATEGORIA
    if (isset($_POST['action']) && $_POST['action'] === 'delete_categoria') {
        header('Content-Type: application/json');
        $id = $_POST['id_categoria'];
        
        // Verifica se tem livros
        $check = $con->query("SELECT count(*) as total FROM livro WHERE categoria = $id");
        $res = $check->fetch_assoc();
        
        if ($res['total'] > 0) {
            echo json_encode(['success'=>false, 'error'=>'Existem livros nesta categoria.']);
        } else {
            $con->query("DELETE FROM categoria WHERE id_categoria = $id");
            echo json_encode(['success'=>true]);
        }
        exit;
    }

    // E. POST NORMAL: GERENCIAR LIVROS (ADD/EDIT/DELETE)
    if (isset($_POST['action'])) {
        $redirect = false; 
        // Caminho absoluto para a pasta de PDFs
        $baseDir = dirname(__DIR__, 4) . '/database/pdfs/';

        // --- EXCLUIR LIVRO ---
        if ($_POST['action'] === 'delete') {
            $id = $_POST['livro_id'];
            
            // Remove Arquivo
            $q = $con->query("SELECT pdf FROM livro WHERE id_livro = $id");
            $f = $q->fetch_assoc();
            if($f && !empty($f['pdf']) && file_exists($baseDir.$f['pdf'])) unlink($baseDir.$f['pdf']);
            
            // Remove Vínculos
            $con->query("DELETE FROM escritor WHERE livro = $id");
            $con->query("DELETE FROM favoritos WHERE livro = $id"); // Se necessário

            if ($con->query("DELETE FROM livro WHERE id_livro = $id")) {
                $_SESSION['feedback_msg'] = "Livro excluído!"; $_SESSION['feedback_type'] = 'success';
            } else {
                $_SESSION['feedback_msg'] = "Erro: " . $con->error; $_SESSION['feedback_type'] = 'error';
            }
            $redirect = true;
        }
        
        // --- ADICIONAR OU EDITAR LIVRO ---
        elseif ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $titulo = $_POST['titulo'];
            $descricao = $_POST['descricao'];
            $data_publi = $_POST['data_publi'];
            $categoria = $_POST['categoria'];
            $autor_id = $_POST['autor']; // ID do Autor
            $action = $_POST['action'];
            $id_livro = $_POST['livro_id'];

            // Upload PDF
            $pdfName = null; $erroUpload = null;
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === 0) {
                $ext = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) === 'pdf') {
                    $newName = uniqid() . ".pdf";
                    if (!is_dir($baseDir)) mkdir($baseDir, 0777, true);
                    
                    if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $baseDir . $newName)) {
                        $pdfName = $newName;
                        // Se edição, apaga antigo
                        if ($action === 'edit') {
                             $qOld = $con->query("SELECT pdf FROM livro WHERE id_livro = $id_livro");
                             $resOld = $qOld->fetch_assoc();
                             if ($resOld && !empty($resOld['pdf']) && file_exists($baseDir.$resOld['pdf'])) unlink($baseDir.$resOld['pdf']);
                        }
                    } else { $erroUpload = "Erro ao salvar PDF na pasta."; }
                }
            }

            if ($erroUpload) {
                $message = $erroUpload; $msgType = 'error';
            } else {
                // 1. Atualiza tabela LIVRO
                if ($action === 'add') {
                    $sql = "INSERT INTO livro (titulo, descricao, data_publi, categoria, pdf) VALUES (?, ?, ?, ?, ?)";
                    $pdfVal = $pdfName ? $pdfName : ''; 
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("sssss", $titulo, $descricao, $data_publi, $categoria, $pdfVal);
                } else {
                    if ($pdfName) {
                        $stmt = $con->prepare("UPDATE livro SET titulo=?, descricao=?, data_publi=?, categoria=?, pdf=? WHERE id_livro=?");
                        $stmt->bind_param("sssssi", $titulo, $descricao, $data_publi, $categoria, $pdfName, $id_livro);
                    } else {
                        $stmt = $con->prepare("UPDATE livro SET titulo=?, descricao=?, data_publi=?, categoria=? WHERE id_livro=?");
                        $stmt->bind_param("ssssi", $titulo, $descricao, $data_publi, $categoria, $id_livro);
                    }
                }

                if ($stmt->execute()) {
                    $livroKey = ($action === 'add') ? $stmt->insert_id : $id_livro;

                    // 2. Atualiza tabela ESCRITOR (Vincula Autor ao Livro)
                    $con->query("DELETE FROM escritor WHERE livro = $livroKey");
                    
                    if (!empty($autor_id)) {
                        $stmtAut = $con->prepare("INSERT INTO escritor (autor, livro) VALUES (?, ?)");
                        $stmtAut->bind_param("ii", $autor_id, $livroKey);
                        $stmtAut->execute();
                    }

                    $_SESSION['feedback_msg'] = "Livro salvo com sucesso!";
                    $_SESSION['feedback_type'] = 'success';
                    $redirect = true;
                } else {
                    $message = "Erro SQL: " . $stmt->error; $msgType = 'error';
                }
            }
        }
        if ($redirect) { header("Location: " . $_SERVER['PHP_SELF']); exit; }
    }
}

// --- BUSCAS DE DADOS ---
$categorias = [];
$res = $con->query("SELECT * FROM categoria ORDER BY nome_categoria");
while ($r = $res->fetch_assoc()) $categorias[] = $r;

$autores = [];
$res = $con->query("SELECT * FROM autor ORDER BY nome_autor");
while ($r = $res->fetch_assoc()) $autores[] = $r;

// Busca Livros + Categoria + Autor
$livros = [];
$sqlLivros = "SELECT l.*, c.nome_categoria, a.nome_autor, a.id_autor 
              FROM livro l 
              LEFT JOIN categoria c ON l.categoria = c.id_categoria 
              LEFT JOIN escritor e ON l.id_livro = e.livro
              LEFT JOIN autor a ON e.autor = a.id_autor
              ORDER BY l.titulo";
$res = $con->query($sqlLivros);
while ($r = $res->fetch_assoc()) $livros[] = $r;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <title>Gerenciar Livros | Admin</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon">
</head>
<body>
    <header id="header-placeholder"></header>

    <main class="painel-admin">
        <?php if (!empty($message)): ?>
            <div class="feedback" style="padding:10px; margin-bottom:15px; color:white; border-radius:5px; background-color: <?php echo ($msgType=='success'?'#28a745':'#dc3545'); ?>;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h2>Adicionar/Editar Livro <button id="btn-toggle-cadastro" type="button">+</button></h2>

        <form class="form-create menu conteudo-oculto" id="form-cadastro" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="livro_id" id="livro_id" value="">

            <div class="valor caixa-texto">
                <label>Título:</label>
                <input type="text" name="titulo" id="titulo" class="valor-texto" required>
                <p id="avisoTitulo" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>Descrição:</label>
                <textarea name="descricao" id="descricao" class="valor-texto" rows="3"></textarea>
                <p id="avisoDescricao" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>Autor:</label>
                <div style="display:flex; gap:5px;">
                    <select name="autor" id="autor" class="valor-texto" style="flex-grow:1;" required>
                        <option value="" disabled selected>Selecione o Autor...</option>
                        <?php foreach ($autores as $aut): ?>
                            <option value="<?php echo $aut['id_autor']; ?>"><?php echo htmlspecialchars($aut['nome_autor']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="showNewAutorForm()" class="btn-small" style="background:#17a2b8; border:none;" title="Novo Autor">+</button>
                    <button type="button" onclick="showManageAutorForm()" class="btn-small" style="background:#6c757d; border:none;" title="Gerenciar Autores">⚙️</button>
                </div>
                <p id="avisoAutor" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>Categoria:</label>
                <div style="display:flex; gap:5px;">
                    <select name="categoria" id="categoria" class="valor-texto" style="flex-grow:1;" required>
                        <option value="" disabled selected>Selecione a Categoria...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nome_categoria']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="showNewCategoryForm()" class="btn-small" style="background:#17a2b8; border:none;" title="Nova Categoria">+</button>
                    <button type="button" onclick="showManageCategoryForm()" class="btn-small" style="background:#6c757d; border:none;" title="Gerenciar Categorias">⚙️</button>
                </div>
                <p id="avisoCategoria" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>Data Publicação:</label>
                <input type="date" name="data_publi" id="data_publi" class="valor-texto" required>
                <p id="avisoDataPubli" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>PDF:</label>
                <input type="file" name="pdf_file" id="pdf_file" class="valor-texto" accept=".pdf">
                <small id="existingPdf" style="color:#666; display:block; margin-top:5px;"></small>
                <p id="avisoPdf" class="aviso"></p>
            </div>

            <div class="interativo">
                <button type="submit" id="btn-menu-criar">Salvar Livro</button>
            </div>
        </form>

        <hr style="margin:20px 0;">

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr><th>Título</th><th>Autor</th><th>Categoria</th><th>Ações</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($livros)): ?>
                        <tr><td colspan="4" align="center">Nenhum livro cadastrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($livro['nome_autor'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($livro['nome_categoria']); ?></td>
                                <td>
                                    <button type="button" onclick='editarLivro(<?php echo json_encode($livro); ?>)' class="btn-small">Editar</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="livro_id" value="<?php echo $livro['id_livro']; ?>">
                                        <button class="btn-delete">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="novaCategoriaModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
            <div class="modal-content" style="background:#fff; margin:15% auto; padding:20px; width:300px; border-radius:5px;">
                <span onclick="hideNewCategoryForm()" style="float:right; cursor:pointer; font-size:24px;">&times;</span>
                <h3>Nova Categoria</h3>
                <input type="text" id="nome_categoria_modal" class="valor-texto" style="width:100%; margin:15px 0;">
                <button onclick="salvarCategoria()" style="width:100%; background:green; color:white; padding:8px;">Salvar</button>
            </div>
        </div>

        <div id="novoAutorModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
            <div class="modal-content" style="background:#fff; margin:15% auto; padding:20px; width:300px; border-radius:5px;">
                <span onclick="hideNewAutorForm()" style="float:right; cursor:pointer; font-size:24px;">&times;</span>
                <h3>Novo Autor</h3>
                <input type="text" id="nome_autor_modal" class="valor-texto" style="width:100%; margin:15px 0;">
                <button onclick="salvarAutor()" style="width:100%; background:green; color:white; padding:8px;">Salvar</button>
            </div>
        </div>

        <div id="gerenciarCategoriaModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
            <div class="modal-content" style="background:#fff; margin:10% auto; padding:20px; width:400px; border-radius:5px; max-height:80vh; overflow-y:auto;">
                <span onclick="hideManageCategoryForm()" style="float:right; cursor:pointer; font-size:24px;">&times;</span>
                <h3>Gerenciar Categorias</h3>
                <table style="width:100%; margin-top:10px;">
                    <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td><?php echo $cat['nome_categoria']; ?></td>
                        <td align="right"><button onclick="excluirCategoria(<?php echo $cat['id_categoria']; ?>)" class="btn-excluir">X</button></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div id="gerenciarAutorModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
            <div class="modal-content" style="background:#fff; margin:10% auto; padding:20px; width:400px; border-radius:5px; max-height:80vh; overflow-y:auto;">
                <span onclick="hideManageAutorForm()" style="float:right; cursor:pointer; font-size:24px;">&times;</span>
                <h3>Gerenciar Autores</h3>
                <table style="width:100%; margin-top:10px;">
                    <?php foreach ($autores as $aut): ?>
                    <tr>
                        <td><?php echo $aut['nome_autor']; ?></td>
                        <td align="right"><button onclick="excluirAutor(<?php echo $aut['id_autor']; ?>)" class="btn-excluir">X</button></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

    </main>

    <script src="/The-Books-On-The-Web/public/scripts/script.js"></script>
    
    <script src="/The-Books-On-The-Web/public/scripts/biblioteca/admin_functions.js"></script>

    <script type="module" src="/The-Books-On-The-Web/public/scripts/biblioteca/livros.js"></script>
</body>
</html>