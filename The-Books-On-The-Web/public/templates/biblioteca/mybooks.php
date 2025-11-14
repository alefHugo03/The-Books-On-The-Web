<?php
session_start();

// Ajuste o caminho conforme sua estrutura de pastas
require_once '../../api/conection/conectionBD.php';

// 1. Segurança: Verificar Login Admin
if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../login/painel_logado.php");
    exit();
}

// --- LÓGICA DE MENSAGEM VIA SESSÃO (Para o F5 funcionar) ---
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

    // A. AJAX: SALVAR NOVA CATEGORIA
    if (isset($_POST['action']) && $_POST['action'] === 'add_categoria') {
        header('Content-Type: application/json');
        $nome = isset($_POST['nome_categoria']) ? trim($_POST['nome_categoria']) : '';

        if (empty($nome)) {
            echo json_encode(['success' => false, 'error' => 'Nome vazio.']);
            exit;
        }

        $stmt = $con->prepare("INSERT INTO categoria (nome_categoria) VALUES (?)");
        $stmt->bind_param("s", $nome);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $stmt->insert_id, 'nome' => $nome]);
        } else {
            echo json_encode(['success' => false, 'error' => $con->error]);
        }
        exit; 
    }

    // B. AJAX: EXCLUIR CATEGORIA
    if (isset($_POST['action']) && $_POST['action'] === 'delete_categoria') {
        header('Content-Type: application/json');
        $id = $_POST['id_categoria'];

        $check = $con->prepare("SELECT count(*) as total FROM livro WHERE categoria = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();

        if ($result['total'] > 0) {
            echo json_encode(['success' => false, 'error' => 'Não é possível excluir: Existem ' . $result['total'] . ' livros nesta categoria.']);
        } else {
            $stmt = $con->prepare("DELETE FROM categoria WHERE id_categoria = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $con->error]);
            }
        }
        exit;
    }

    // C. POST NORMAL: GERENCIAR LIVROS
    if (isset($_POST['action'])) {
        
        $redirect = false; 

        // CAMINHO DA PASTA DATABASE/PDFS (Volta 3 níveis até a raiz do projeto)
        $baseDir = dirname(__DIR__, 3) . '/database/pdfs/';

        // --- EXCLUIR LIVRO ---
        if ($_POST['action'] === 'delete') {
            $id = $_POST['livro_id'];

            // 1. Deletar Arquivo Físico
            $queryFile = $con->prepare("SELECT pdf FROM livro WHERE id_livro = ?");
            $queryFile->bind_param("i", $id);
            $queryFile->execute();
            $resFile = $queryFile->get_result()->fetch_assoc();

            if ($resFile && !empty($resFile['pdf'])) {
                $filePath = $baseDir . $resFile['pdf'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            // 2. Deletar do Banco
            $stmt = $con->prepare("DELETE FROM livro WHERE id_livro = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $_SESSION['feedback_msg'] = "Livro excluído com sucesso!";
                $_SESSION['feedback_type'] = 'success';
            } else {
                $_SESSION['feedback_msg'] = "Erro ao excluir: " . $con->error;
                $_SESSION['feedback_type'] = 'error';
            }
            $redirect = true;
        }
        
        // --- ADICIONAR OU EDITAR LIVRO ---
        elseif ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $titulo = $_POST['titulo'];
            $descricao = $_POST['descricao'];
            $data_publi = $_POST['data_publi'];
            $categoria = $_POST['categoria'];
            $action = $_POST['action'];
            $id_livro = $_POST['livro_id'];

            // Upload PDF
            $pdfName = null;
            $erroUpload = null;

            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === 0) {
                $ext = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) === 'pdf') {
                    $newName = uniqid() . ".pdf";
                    
                    // Cria a pasta database/pdfs se não existir
                    if (!is_dir($baseDir)) {
                        mkdir($baseDir, 0777, true);
                    }

                    if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $baseDir . $newName)) {
                        $pdfName = $newName;

                        // Se for edição, apaga o PDF antigo
                        if ($action === 'edit') {
                             $queryOld = $con->prepare("SELECT pdf FROM livro WHERE id_livro = ?");
                             $queryOld->bind_param("i", $id_livro);
                             $queryOld->execute();
                             $resOld = $queryOld->get_result()->fetch_assoc();
                             if ($resOld && !empty($resOld['pdf'])) {
                                 $oldPath = $baseDir . $resOld['pdf'];
                                 if (file_exists($oldPath)) {
                                     unlink($oldPath);
                                 }
                             }
                        }
                    } else {
                        $erroUpload = "Erro ao salvar arquivo na pasta database/pdfs.";
                    }
                }
            }

            if ($erroUpload) {
                $message = $erroUpload;
                $msgType = 'error';
            } else {
                if ($action === 'add') {
                    if ($pdfName) {
                        $sql = "INSERT INTO livro (titulo, descricao, data_publi, categoria, pdf) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("sssss", $titulo, $descricao, $data_publi, $categoria, $pdfName);
                    } else {
                        $sql = "INSERT INTO livro (titulo, descricao, data_publi, categoria) VALUES (?, ?, ?, ?)";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("ssss", $titulo, $descricao, $data_publi, $categoria);
                    }
                    $msgSuccess = "Livro adicionado com sucesso!";
                } else {
                    if ($pdfName) {
                        $sql = "UPDATE livro SET titulo=?, descricao=?, data_publi=?, categoria=?, pdf=? WHERE id_livro=?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("sssssi", $titulo, $descricao, $data_publi, $categoria, $pdfName, $id_livro);
                    } else {
                        $sql = "UPDATE livro SET titulo=?, descricao=?, data_publi=?, categoria=? WHERE id_livro=?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("ssssi", $titulo, $descricao, $data_publi, $categoria, $id_livro);
                    }
                    $msgSuccess = "Livro atualizado com sucesso!";
                }

                if ($stmt->execute()) {
                    $_SESSION['feedback_msg'] = $msgSuccess;
                    $_SESSION['feedback_type'] = 'success';
                    $redirect = true;
                } else {
                    $message = "Erro no banco: " . $stmt->error;
                    $msgType = 'error';
                }
            }
        }

        if ($redirect) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// --- BUSCAS ---
$categorias = [];
$resCat = mysqli_query($con, "SELECT * FROM categoria ORDER BY nome_categoria");
if($resCat) while ($row = mysqli_fetch_assoc($resCat)) $categorias[] = $row;

$livros = [];
$resLiv = mysqli_query($con, "SELECT l.*, c.nome_categoria FROM livro l LEFT JOIN categoria c ON l.categoria = c.id_categoria ORDER BY l.titulo");
if($resLiv) while ($row = mysqli_fetch_assoc($resLiv)) $livros[] = $row;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Livros</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/livros.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon">
</head>
<body>
    <header id="header-placeholder"></header>

    <main class="painel-admin">
        
        <?php if (!empty($message)): ?>
            <div class="feedback <?php echo $msgType; ?>" style="padding:10px; margin-bottom:15px; color:white; border-radius:5px; background-color: <?php echo ($msgType=='success'?'#28a745':'#dc3545'); ?>;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h2>Adicionar/Editar Livro <button id="btn-toggle-cadastro" type="button">+</button></h2>

        <form class="form-create menu conteudo-oculto" id="form-cadastro" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="livro_id" id="livro_id" value="">

            <div class="valor caixa-texto">
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" class="valor-texto" required>
                <p id="avisoTitulo" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao" class="valor-texto" rows="3"></textarea>
                <p id="avisoDescricao" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label for="data_publi">Data Publicação:</label>
                <input type="date" name="data_publi" id="data_publi" class="valor-texto" required>
                <p id="avisoDataPubli" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label for="categoria">Categoria:</label>
                <div style="display:flex; gap:5px;">
                    <select name="categoria" id="categoria" class="valor-texto" style="flex-grow:1;" required>
                        <option value="" disabled selected>Selecione...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nome_categoria']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" onclick="showNewCategoryForm()" class="btn-small" style="background:#17a2b8; color:white; border:none;" title="Nova">+</button>
                    <button type="button" onclick="showManageCategoryForm()" class="btn-small" style="background:#6c757d; color:white; border:none;" title="Gerenciar">⚙️</button>
                </div>
                <p id="avisoCategoria" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label for="pdf_file">PDF:</label>
                <input type="file" name="pdf_file" id="pdf_file" class="valor-texto" accept=".pdf">
                <small id="existingPdf" style="display:block; color:#666; margin-top:5px;"></small>
                <p id="avisoPdf" class="aviso"></p>
            </div>

            <div class="interativo">
                <button type="submit" id="btn-menu-criar">Salvar Livro</button>
                <p id="aviso" class="aviso"></p>
            </div>
        </form>

        <hr style="margin:20px 0; border:0; border-top:1px solid #ddd;">

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Título</th><th>Categoria</th><th>Data</th><th>PDF</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($livros)): ?>
                        <tr><td colspan="5" align="center">Nenhum livro cadastrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($livro['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($livro['nome_categoria']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($livro['data_publi'])); ?></td>
                                <td>
                                    <?php if (!empty($livro['pdf'])): ?>
                                        <a href="../database/pdfs/<?php echo $livro['pdf']; ?>" target="_blank">Ver</a>
                                    <?php else: ?> - <?php endif; ?>
                                </td>
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
                <span class="close" onclick="hideNewCategoryForm()" style="float:right; cursor:pointer; font-size:24px;">&times;</span>
                <h3>Nova Categoria</h3>
                <input type="text" id="nome_categoria_modal" class="valor-texto" style="width:100%; margin:15px 0;">
                <button onclick="salvarCategoria()" style="width:100%; background:green; color:white; padding:8px;">Salvar</button>
            </div>
        </div>

        <div id="gerenciarCategoriaModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
            <div class="modal-content" style="background:#fff; margin:10% auto; padding:20px; width:400px; border-radius:5px; max-height:80vh; overflow-y:auto;">
                <span onclick="hideManageCategoryForm()" style="float:right; cursor:pointer; font-size:24px;">&times;</span>
                <h3>Gerenciar Categorias</h3>
                <table style="width:100%; border-collapse:collapse; margin-top:15px;">
                    <thead style="background:#f0f0f0;">
                        <tr><th style="padding:8px;">Nome</th><th style="text-align:right; padding:8px;">Ação</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px;"><?php echo htmlspecialchars($cat['nome_categoria']); ?></td>
                            <td style="text-align:right; padding:8px;">
                                <button onclick="excluirCategoria(<?php echo $cat['id_categoria']; ?>)" style="color:red; background:none; border:none; cursor:pointer; font-weight:bold;">Excluir</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="scripts/script.js"></script>
    <script type="module" src="scripts/biblioteca/livros.js"></script>
    <script src="scripts/animations/ocultar.js"></script>
</body>
</html>