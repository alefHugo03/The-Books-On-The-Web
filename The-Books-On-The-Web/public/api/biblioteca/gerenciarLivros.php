<?php
// ARQUIVO: public/api/biblioteca/gerenciarLivros.php
ob_start(); // Inicia buffer para evitar vazamento de HTML
session_start();

// Ajuste o caminho para conectar ao banco. 
// Estando em public/api/biblioteca, voltamos uma pasta (..) para api, e entramos em conection.
require_once '../conection/conectionBD.php'; 

// Define cabeçalho JSON
header('Content-Type: application/json');

// Verificação de Segurança
if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// ============================================================================
// GET: BUSCAR DADOS (Preencher tabelas e selects)
// ============================================================================
if ($method === 'GET') {
    try {
        $acao = $_GET['acao'] ?? 'listar_tudo';

        if ($acao === 'listar_tudo') {
            // 1. Categorias
            $cats = [];
            $res = $con->query("SELECT * FROM categoria ORDER BY nome_categoria");
            while ($r = $res->fetch_assoc()) $cats[] = $r;

            // 2. Autores
            $auts = [];
            $res = $con->query("SELECT * FROM autor ORDER BY nome_autor");
            while ($r = $res->fetch_assoc()) $auts[] = $r;

            // 3. Livros
            $livros = [];
            $sqlLivros = "SELECT l.*, c.nome_categoria, a.nome_autor, a.id_autor 
                          FROM livro l 
                          LEFT JOIN categoria c ON l.categoria = c.id_categoria 
                          LEFT JOIN escritor e ON l.id_livro = e.livro
                          LEFT JOIN autor a ON e.autor = a.id_autor
                          ORDER BY l.titulo DESC"; // Ordem decrescente para ver os novos primeiro
            $res = $con->query($sqlLivros);
            while ($r = $res->fetch_assoc()) $livros[] = $r;

            echo json_encode([
                'success' => true,
                'categorias' => $cats,
                'autores' => $auts,
                'livros' => $livros
            ]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// ============================================================================
// POST: SALVAR, EDITAR, EXCLUIR
// ============================================================================
if ($method === 'POST') {
    ob_clean(); // Limpa qualquer lixo antes de processar
    
    $action = $_POST['action'] ?? '';

    // --- DEFINIÇÃO DA PASTA DE UPLOAD (Absoluta) ---
    // __DIR__ é '.../public/api/biblioteca'
    // Voltamos 3 níveis para chegar na raiz do projeto: '.../The-Books-On-The-Web'
    $rootDir = dirname(__DIR__, 3); 
    $uploadDir = $rootDir . '/database/pdfs/';

    // Garante que a pasta existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // 1. ADICIONAR OU EDITAR LIVRO
    if ($action === 'add' || $action === 'edit') {
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $categoria = $_POST['categoria'];
        $autorId = $_POST['autor'];
        $dataPubli = $_POST['data_publi'];
        $idLivro = $_POST['livro_id'] ?? null;

        // Upload PDF
        $pdfName = null;
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === 0) {
            $ext = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) === 'pdf') {
                $newName = uniqid() . ".pdf";
                if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $uploadDir . $newName)) {
                    $pdfName = $newName;
                }
            }
        }

        if ($action === 'add') {
            if (!$pdfName) { echo json_encode(['success'=>false, 'error'=>'PDF obrigatório.']); exit; }
            
            $stmt = $con->prepare("INSERT INTO livro (titulo, descricao, categoria, data_publi, pdf) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $titulo, $descricao, $categoria, $dataPubli, $pdfName);
            
            if ($stmt->execute()) {
                $novoId = $stmt->insert_id;
                $con->query("INSERT INTO escritor (livro, autor) VALUES ($novoId, $autorId)");
                echo json_encode(['success'=>true, 'msg'=>'Livro criado com sucesso!']);
            } else {
                echo json_encode(['success'=>false, 'error'=>$stmt->error]);
            }
        } 
        else { // Edit
            if ($pdfName) {
                $stmt = $con->prepare("UPDATE livro SET titulo=?, descricao=?, categoria=?, data_publi=?, pdf=? WHERE id_livro=?");
                $stmt->bind_param("ssissi", $titulo, $descricao, $categoria, $dataPubli, $pdfName, $idLivro);
            } else {
                $stmt = $con->prepare("UPDATE livro SET titulo=?, descricao=?, categoria=?, data_publi=? WHERE id_livro=?");
                $stmt->bind_param("ssisi", $titulo, $descricao, $categoria, $dataPubli, $idLivro);
            }
            
            if ($stmt->execute()) {
                $con->query("DELETE FROM escritor WHERE livro = $idLivro");
                $con->query("INSERT INTO escritor (livro, autor) VALUES ($idLivro, $autorId)");
                echo json_encode(['success'=>true, 'msg'=>'Livro atualizado!']);
            } else {
                echo json_encode(['success'=>false, 'error'=>$stmt->error]);
            }
        }
        exit;
    }

    // 2. EXCLUIR LIVRO
    if ($action === 'delete') {
        $id = intval($_POST['livro_id']);
        
        // Busca nome do PDF antes de apagar
        $q = $con->query("SELECT pdf FROM livro WHERE id_livro = $id");
        $l = $q->fetch_assoc();

        $con->query("DELETE FROM escritor WHERE livro = $id");
        if ($con->query("DELETE FROM livro WHERE id_livro = $id")) {
            if($l && $l['pdf']) {
                $file = $uploadDir . $l['pdf'];
                if(file_exists($file)) unlink($file);
            }
            echo json_encode(['success'=>true, 'msg'=>'Livro excluído!']);
        } else {
            echo json_encode(['success'=>false, 'error'=>$con->error]);
        }
        exit;
    }

    // 3. CATEGORIAS
    if ($action === 'add_categoria') {
        $nome = trim($_POST['nome_categoria']);
        if(empty($nome)) { echo json_encode(['success'=>false, 'error'=>'Nome vazio']); exit; }
        
        $con->query("INSERT INTO categoria (nome_categoria) VALUES ('$nome')");
        echo json_encode(['success'=>true]);
        exit;
    }
    if ($action === 'delete_categoria') {
        $id = intval($_POST['id_categoria']);
        $check = $con->query("SELECT count(*) as t FROM livro WHERE categoria = $id")->fetch_assoc();
        if ($check['t'] > 0) echo json_encode(['success'=>false, 'error'=>'Categoria tem livros vinculados.']);
        else { $con->query("DELETE FROM categoria WHERE id_categoria = $id"); echo json_encode(['success'=>true]); }
        exit;
    }

    // 4. AUTORES
    if ($action === 'add_autor') {
        $nome = trim($_POST['nome_autor']);
        if(empty($nome)) { echo json_encode(['success'=>false, 'error'=>'Nome vazio']); exit; }
        
        $con->query("INSERT INTO autor (nome_autor) VALUES ('$nome')");
        echo json_encode(['success'=>true]);
        exit;
    }
    if ($action === 'delete_autor') {
        $id = intval($_POST['id_autor']);
        $con->query("DELETE FROM escritor WHERE autor = $id");
        $con->query("DELETE FROM autor WHERE id_autor = $id");
        echo json_encode(['success'=>true]);
        exit;
    }
}
?>