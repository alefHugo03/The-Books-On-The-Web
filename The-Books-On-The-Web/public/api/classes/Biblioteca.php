<?php
// ARQUIVO: public/api/classes/Biblioteca.php

class Biblioteca {
    private $db;
    private $uploadDir;

    public function __construct($conexao) {
        $this->db = $conexao;
        $this->uploadDir = dirname(__DIR__, 3) . '/database/pdfs/';
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // --- 1. LISTAGEM GERAL ---
    public function listarTudo() {
        $dados = [];

        // Busca Listas para os Selects
        $dados['categorias'] = $this->db->query("SELECT * FROM CATEGORIA ORDER BY nome_categoria")->fetch_all(MYSQLI_ASSOC);
        $dados['autores']    = $this->db->query("SELECT * FROM AUTOR ORDER BY nome_autor")->fetch_all(MYSQLI_ASSOC);
        // NOVO: Busca Editoras
        $dados['editoras']   = $this->db->query("SELECT * FROM editora ORDER BY nome_editora")->fetch_all(MYSQLI_ASSOC);

        // Busca Livros (Agora com nome da Editora)
        $sql = "SELECT l.*, 
                       ed.nome_editora,
                       GROUP_CONCAT(DISTINCT c.nome_categoria SEPARATOR ', ') as nomes_categorias,
                       GROUP_CONCAT(DISTINCT c.id_categoria) as ids_categorias,
                       GROUP_CONCAT(DISTINCT a.nome_autor SEPARATOR ', ') as nomes_autores,
                       GROUP_CONCAT(DISTINCT a.id_autor) as ids_autores
                FROM LIVRO l 
                LEFT JOIN editora ed ON l.fk_editora = ed.id_editora
                LEFT JOIN Temas t ON l.id_livro = t.fk_LIVRO_id_livro
                LEFT JOIN CATEGORIA c ON t.fk_CATEGORIA_id_categoria = c.id_categoria
                LEFT JOIN ESCRITOR e ON l.id_livro = e.FK_LIVRO_id_livro
                LEFT JOIN AUTOR a ON e.FK_AUTOR_id_autor = a.id_autor
                GROUP BY l.id_livro
                ORDER BY l.titulo DESC";
        
        $res = $this->db->query($sql);
        $dados['livros'] = $res->fetch_all(MYSQLI_ASSOC);
        
        return $dados;
    }

    // --- 2. SALVAR LIVRO ---
    public function salvarLivro($post, $files) {
        $titulo = $post['titulo'];
        $descricao = $post['descricao'];
        $dataPubli = $post['data_publi'];
        $editoraId = !empty($post['editora']) ? intval($post['editora']) : null; // NOVO
        $idLivro = $post['livro_id'] ?? '';
        $acao = $post['action'];
        
        $categoriasIds = $post['categoria'] ?? []; 
        $autoresIds = $post['autor'] ?? [];       

        if (empty($categoriasIds) || empty($autoresIds)) {
            return ['success' => false, 'error' => 'Selecione pelo menos um autor e uma categoria.'];
        }

        // Upload PDF
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

        // --- INSERÇÃO (ADD) ---
        if ($acao === 'add') {
            if (!$pdfName) return ['success' => false, 'error' => 'O arquivo PDF é obrigatório.'];

            // NOVO: Adicionado fk_editora
            $stmt = $this->db->prepare("INSERT INTO LIVRO (titulo, descricao, data_publi, fk_editora, pdf) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $titulo, $descricao, $dataPubli, $editoraId, $pdfName);

            if ($stmt->execute()) {
                $novoId = $stmt->insert_id;
                $this->vincularRelacoes($novoId, $autoresIds, $categoriasIds);
                return ['success' => true, 'msg' => 'Livro criado com sucesso!'];
            }
        } 
        // --- EDIÇÃO (EDIT) ---
        elseif ($acao === 'edit') {
            // NOVO: Atualiza fk_editora
            if ($pdfName) {
                $stmt = $this->db->prepare("UPDATE LIVRO SET titulo=?, descricao=?, data_publi=?, fk_editora=?, pdf=? WHERE id_livro=?");
                $stmt->bind_param("sssisi", $titulo, $descricao, $dataPubli, $editoraId, $pdfName, $idLivro);
            } else {
                $stmt = $this->db->prepare("UPDATE LIVRO SET titulo=?, descricao=?, data_publi=?, fk_editora=? WHERE id_livro=?");
                $stmt->bind_param("sssii", $titulo, $descricao, $dataPubli, $editoraId, $idLivro);
            }

            if ($stmt->execute()) {
                $this->vincularRelacoes($idLivro, $autoresIds, $categoriasIds);
                return ['success' => true, 'msg' => 'Livro atualizado!'];
            }
        }

        return ['success' => false, 'error' => 'Erro no banco de dados: ' . $this->db->error];
    }

    // --- MÉTODOS AUXILIARES ---

    private function vincularRelacoes($idLivro, $autores, $categorias) {
        $this->db->query("DELETE FROM ESCRITOR WHERE FK_LIVRO_id_livro = $idLivro");
        $this->db->query("DELETE FROM Temas WHERE fk_LIVRO_id_livro = $idLivro");

        if (is_array($autores)) {
            foreach ($autores as $autorId) {
                $aid = intval($autorId);
                $this->db->query("INSERT INTO ESCRITOR (FK_LIVRO_id_livro, FK_AUTOR_id_autor) VALUES ($idLivro, $aid)");
            }
        }
        if (is_array($categorias)) {
            foreach ($categorias as $catId) {
                $cid = intval($catId);
                $this->db->query("INSERT INTO Temas (fk_LIVRO_id_livro, fk_CATEGORIA_id_categoria) VALUES ($idLivro, $cid)");
            }
        }
    }

    public function excluirLivro($id) {
        $id = intval($id);
        $q = $this->db->query("SELECT pdf FROM LIVRO WHERE id_livro = $id");
        $livro = $q->fetch_assoc();

        $this->db->query("DELETE FROM ESCRITOR WHERE FK_LIVRO_id_livro = $id");
        $this->db->query("DELETE FROM Temas WHERE fk_LIVRO_id_livro = $id");
        
        if ($this->db->query("DELETE FROM LIVRO WHERE id_livro = $id")) {
            if ($livro && $livro['pdf']) {
                $file = $this->uploadDir . $livro['pdf'];
                if (file_exists($file)) unlink($file);
            }
            return ['success' => true, 'msg' => 'Livro excluído!'];
        }
        return ['success' => false, 'error' => 'Erro ao excluir: ' . $this->db->error];
    }

    public function gerenciarAuxiliar($tipo, $acao, $id = null, $nome = null) {
        // NOVO: Suporte para tabela editora
        if ($tipo === 'categoria') {
            $tabela = 'CATEGORIA'; $colId = 'id_categoria'; $colNome = 'nome_categoria';
        } elseif ($tipo === 'autor') {
            $tabela = 'AUTOR'; $colId = 'id_autor'; $colNome = 'nome_autor';
        } else {
            $tabela = 'editora'; $colId = 'id_editora'; $colNome = 'nome_editora';
        }

        if ($acao === 'add') {
            if(empty($nome)) return ['success'=>false, 'error'=>'Nome vazio'];
            $stmt = $this->db->prepare("INSERT INTO $tabela ($colNome) VALUES (?)");
            $stmt->bind_param("s", $nome);
            return $stmt->execute() ? ['success'=>true] : ['success'=>false, 'error'=>$this->db->error];
        }

        if ($acao === 'delete') {
            // Limpeza de FKs antes de deletar
            if ($tipo === 'categoria') $this->db->query("DELETE FROM Temas WHERE fk_CATEGORIA_id_categoria = $id");
            elseif ($tipo === 'autor') $this->db->query("DELETE FROM ESCRITOR WHERE FK_AUTOR_id_autor = $id");
            elseif ($tipo === 'editora') $this->db->query("UPDATE LIVRO SET fk_editora = NULL WHERE fk_editora = $id");

            $this->db->query("DELETE FROM $tabela WHERE $colId = $id");
            return ['success'=>true];
        }
    }
}
?>