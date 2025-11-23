<?php
// ARQUIVO: public/api/classes/Biblioteca.php

class Biblioteca {
    private $db;
    private $uploadDir;

    public function __construct($conexao) {
        $this->db = $conexao;
        $this->uploadDir = dirname(__DIR__, 3) . '/database/pdfs/';
        if (!is_dir($this->uploadDir)) { @mkdir($this->uploadDir, 0777, true); }
    }

    // --- 1. LISTAR TUDO ---
    public function listarTudo() {
        $dados = [];

        // Listas
        $dados['categorias'] = $this->db->query("SELECT * FROM categoria ORDER BY nome_categoria")->fetch_all(MYSQLI_ASSOC);
        $dados['autores']    = $this->db->query("SELECT * FROM autor ORDER BY nome_autor")->fetch_all(MYSQLI_ASSOC);
        $dados['editoras']   = $this->db->query("SELECT * FROM editora ORDER BY nome_editora")->fetch_all(MYSQLI_ASSOC);

        // Busca Principal (AGORA COM NOMES PADRONIZADOS NO BANCO)
        // livro.id_editora, temas.id_livro, temas.id_categoria...
        $sql = "SELECT l.*, 
                       ed.nome_editora,
                       GROUP_CONCAT(DISTINCT c.nome_categoria SEPARATOR ', ') as nomes_categorias,
                       GROUP_CONCAT(DISTINCT c.id_categoria) as ids_categorias,
                       GROUP_CONCAT(DISTINCT a.nome_autor SEPARATOR ', ') as nomes_autores,
                       GROUP_CONCAT(DISTINCT a.id_autor) as ids_autores
                FROM livro l 
                LEFT JOIN editora ed ON l.id_editora = ed.id_editora
                LEFT JOIN temas t ON l.id_livro = t.id_livro
                LEFT JOIN categoria c ON t.id_categoria = c.id_categoria
                LEFT JOIN escritor e ON l.id_livro = e.id_livro
                LEFT JOIN autor a ON e.id_autor = a.id_autor
                GROUP BY l.id_livro, ed.nome_editora
                ORDER BY l.titulo ASC";
        
        $res = $this->db->query($sql);
        $dados['livros'] = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        
        return $dados;
    }

    // --- 2. SALVAR LIVRO ---
    public function salvarLivro($post, $files) {
        $titulo = $post['titulo'];
        $descricao = $post['descricao'];
        $dataPubli = $post['data_publi'];
        $editoraId = !empty($post['editora']) ? intval($post['editora']) : null;
        $idLivro = $post['livro_id'] ?? '';
        $acao = $post['action'];
        
        $categoriasIds = isset($post['categoria']) ? (is_array($post['categoria']) ? $post['categoria'] : [$post['categoria']]) : [];
        $autoresIds = isset($post['autor']) ? (is_array($post['autor']) ? $post['autor'] : [$post['autor']]) : [];

        if (empty($categoriasIds) || empty($autoresIds)) {
            return ['success' => false, 'error' => 'Selecione Autor e Categoria.'];
        }

        // Upload
        $pdfName = null;
        if (isset($files['pdf_file']) && $files['pdf_file']['error'] === 0) {
            $ext = pathinfo($files['pdf_file']['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) === 'pdf') {
                $newName = uniqid() . ".pdf";
                if (move_uploaded_file($files['pdf_file']['tmp_name'], $this->uploadDir . $newName)) {
                    $pdfName = $newName;
                }
            }
        }

        // ADD (id_editora)
        if ($acao === 'add') {
            if (!$pdfName) return ['success' => false, 'error' => 'PDF obrigatório.'];

            $stmt = $this->db->prepare("INSERT INTO livro (titulo, descricao, data_publi, id_editora, pdf) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $titulo, $descricao, $dataPubli, $editoraId, $pdfName);

            if ($stmt->execute()) {
                $novoId = $stmt->insert_id;
                $this->vincularRelacoes($novoId, $autoresIds, $categoriasIds);
                return ['success' => true, 'msg' => 'Cadastrado com sucesso!'];
            }
        } 
        // EDIT (id_editora)
        elseif ($acao === 'edit') {
            if ($pdfName) {
                $stmt = $this->db->prepare("UPDATE livro SET titulo=?, descricao=?, data_publi=?, id_editora=?, pdf=? WHERE id_livro=?");
                $stmt->bind_param("sssisi", $titulo, $descricao, $dataPubli, $editoraId, $pdfName, $idLivro);
            } else {
                $stmt = $this->db->prepare("UPDATE livro SET titulo=?, descricao=?, data_publi=?, id_editora=? WHERE id_livro=?");
                $stmt->bind_param("sssii", $titulo, $descricao, $dataPubli, $editoraId, $idLivro);
            }

            if ($stmt->execute()) {
                $this->vincularRelacoes($idLivro, $autoresIds, $categoriasIds);
                return ['success' => true, 'msg' => 'Atualizado com sucesso!'];
            }
        }

        return ['success' => false, 'error' => 'Erro Banco: ' . $this->db->error];
    }

    // --- AUXILIARES (Agora usando nomes limpos) ---
    private function vincularRelacoes($idLivro, $autores, $categorias) {
        $this->db->query("DELETE FROM escritor WHERE id_livro = $idLivro");
        $this->db->query("DELETE FROM temas WHERE id_livro = $idLivro");

        if (!empty($autores)) {
            $stmt = $this->db->prepare("INSERT INTO escritor (id_livro, id_autor) VALUES (?, ?)");
            foreach ($autores as $aid) {
                $aid = intval($aid);
                if($aid > 0) { $stmt->bind_param("ii", $idLivro, $aid); $stmt->execute(); }
            }
        }
        if (!empty($categorias)) {
            $stmt = $this->db->prepare("INSERT INTO temas (id_livro, id_categoria) VALUES (?, ?)");
            foreach ($categorias as $cid) {
                $cid = intval($cid);
                if($cid > 0) { $stmt->bind_param("ii", $idLivro, $cid); $stmt->execute(); }
            }
        }
    }

    public function excluirLivro($id) {
        $id = intval($id);
        $q = $this->db->query("SELECT pdf FROM livro WHERE id_livro = $id");
        $livro = $q->fetch_assoc();

        $this->db->query("DELETE FROM escritor WHERE id_livro = $id");
        $this->db->query("DELETE FROM temas WHERE id_livro = $id");
        
        if ($this->db->query("DELETE FROM livro WHERE id_livro = $id")) {
            if ($livro && !empty($livro['pdf'])) {
                $file = $this->uploadDir . $livro['pdf'];
                if (file_exists($file)) @unlink($file);
            }
            return ['success' => true, 'msg' => 'Excluído!'];
        }
        return ['success' => false, 'error' => 'Erro SQL: ' . $this->db->error];
    }

    public function gerenciarAuxiliar($tipo, $acao, $id = null, $nome = null) {
        if ($tipo === 'categoria') {
            $tabela = 'categoria'; $colId = 'id_categoria'; $colNome = 'nome_categoria';
        } elseif ($tipo === 'autor') {
            $tabela = 'autor'; $colId = 'id_autor'; $colNome = 'nome_autor';
        } elseif ($tipo === 'editora') {
            $tabela = 'editora'; $colId = 'id_editora'; $colNome = 'nome_editora';
        } else {
            return ['success'=>false, 'error'=>'Tipo inválido'];
        }

        if ($acao === 'add') {
            if(empty($nome)) return ['success'=>false, 'error'=>'Nome vazio'];
            $stmt = $this->db->prepare("INSERT INTO $tabela ($colNome) VALUES (?)");
            $stmt->bind_param("s", $nome);
            return $stmt->execute() ? ['success'=>true] : ['success'=>false, 'error'=>$this->db->error];
        }

        if ($acao === 'delete') {
            // Limpeza de dependências com nomes novos
            if ($tipo === 'categoria') $this->db->query("DELETE FROM temas WHERE id_categoria = $id");
            elseif ($tipo === 'autor') $this->db->query("DELETE FROM escritor WHERE id_autor = $id");
            elseif ($tipo === 'editora') $this->db->query("UPDATE livro SET id_editora = NULL WHERE id_editora = $id");

            return $this->db->query("DELETE FROM $tabela WHERE $colId = $id") 
                ? ['success'=>true] 
                : ['success'=>false, 'error'=>$this->db->error];
        }
    }
}
?> 