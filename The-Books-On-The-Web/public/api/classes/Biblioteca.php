<?php
// ARQUIVO: public/api/classes/Biblioteca.php

class Biblioteca {
    private $db;
    private $uploadDir;

    public function __construct($conexao) {
        $this->db = $conexao;
        // Define o caminho de upload uma vez só (subindo níveis a partir daqui)
        $this->uploadDir = dirname(__DIR__, 3) . '/database/pdfs/';
        
        // Garante que a pasta existe
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // --- 1. LISTAGEM GERAL (Para preencher a tela admin) ---
    public function listarTudo() {
        $dados = [];

        // Busca Categorias
        $res = $this->db->query("SELECT * FROM categoria ORDER BY nome_categoria");
        $dados['categorias'] = $res->fetch_all(MYSQLI_ASSOC);

        // Busca Autores
        $res = $this->db->query("SELECT * FROM autor ORDER BY nome_autor");
        $dados['autores'] = $res->fetch_all(MYSQLI_ASSOC);

        // Busca Livros (com JOINs para pegar nomes)
        $sql = "SELECT l.*, c.nome_categoria, a.nome_autor, a.id_autor 
                FROM livro l 
                LEFT JOIN categoria c ON l.categoria = c.id_categoria 
                LEFT JOIN escritor e ON l.id_livro = e.livro
                LEFT JOIN autor a ON e.autor = a.id_autor
                ORDER BY l.titulo DESC";
        
        $res = $this->db->query($sql);
        $dados['livros'] = $res->fetch_all(MYSQLI_ASSOC);
        
        return $dados;
    }

    // --- 2. SALVAR LIVRO (CRIAR OU EDITAR) ---
    public function salvarLivro($post, $files) {
        $titulo = $post['titulo'];
        $descricao = $post['descricao'];
        $categoria = $post['categoria'];
        $autorId = $post['autor'];
        $dataPubli = $post['data_publi'];
        $idLivro = $post['livro_id'] ?? '';
        $acao = $post['action']; // 'add' ou 'edit'

        // Lógica de Upload do PDF
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

        // MODO CRIAÇÃO
        if ($acao === 'add') {
            if (!$pdfName) return ['success' => false, 'error' => 'O arquivo PDF é obrigatório.'];

            $stmt = $this->db->prepare("INSERT INTO livro (titulo, descricao, categoria, data_publi, pdf) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $titulo, $descricao, $categoria, $dataPubli, $pdfName);

            if ($stmt->execute()) {
                $novoId = $stmt->insert_id;
                // Vincula autor
                $this->db->query("INSERT INTO escritor (livro, autor) VALUES ($novoId, $autorId)");
                return ['success' => true, 'msg' => 'Livro criado com sucesso!'];
            }
        } 
        // MODO EDIÇÃO
        elseif ($acao === 'edit') {
            if ($pdfName) {
                // Atualiza com PDF novo
                $stmt = $this->db->prepare("UPDATE livro SET titulo=?, descricao=?, categoria=?, data_publi=?, pdf=? WHERE id_livro=?");
                $stmt->bind_param("ssissi", $titulo, $descricao, $categoria, $dataPubli, $pdfName, $idLivro);
            } else {
                // Mantém PDF antigo
                $stmt = $this->db->prepare("UPDATE livro SET titulo=?, descricao=?, categoria=?, data_publi=? WHERE id_livro=?");
                $stmt->bind_param("ssisi", $titulo, $descricao, $categoria, $dataPubli, $idLivro);
            }

            if ($stmt->execute()) {
                // Atualiza autor (Remove vínculo antigo e cria novo)
                $this->db->query("DELETE FROM escritor WHERE livro = $idLivro");
                $this->db->query("INSERT INTO escritor (livro, autor) VALUES ($idLivro, $autorId)");
                return ['success' => true, 'msg' => 'Livro atualizado!'];
            }
        }

        return ['success' => false, 'error' => 'Erro no banco de dados: ' . $this->db->error];
    }

    // --- 3. EXCLUIR LIVRO ---
    public function excluirLivro($id) {
        $id = intval($id);

        // 1. Pega o nome do PDF para apagar o arquivo
        $q = $this->db->query("SELECT pdf FROM livro WHERE id_livro = $id");
        $livro = $q->fetch_assoc();

        // 2. Remove do banco
        $this->db->query("DELETE FROM escritor WHERE livro = $id"); // Remove relação autor
        
        if ($this->db->query("DELETE FROM livro WHERE id_livro = $id")) {
            // 3. Remove o arquivo físico
            if ($livro && $livro['pdf']) {
                $file = $this->uploadDir . $livro['pdf'];
                if (file_exists($file)) unlink($file);
            }
            return ['success' => true, 'msg' => 'Livro e PDF excluídos!'];
        }
        
        return ['success' => false, 'error' => 'Erro ao excluir: ' . $this->db->error];
    }

    // --- 4. GERENCIAR AUXILIARES (Categoria e Autor) ---
    public function gerenciarAuxiliar($tipo, $acao, $id = null, $nome = null) {
        $tabela = ($tipo === 'categoria') ? 'categoria' : 'autor';
        $colId  = ($tipo === 'categoria') ? 'id_categoria' : 'id_autor';
        $colNome= ($tipo === 'categoria') ? 'nome_categoria' : 'nome_autor';

        if ($acao === 'add') {
            if(empty($nome)) return ['success'=>false, 'error'=>'Nome vazio'];
            $stmt = $this->db->prepare("INSERT INTO $tabela ($colNome) VALUES (?)");
            $stmt->bind_param("s", $nome);
            return $stmt->execute() ? ['success'=>true] : ['success'=>false, 'error'=>$this->db->error];
        }

        if ($acao === 'delete') {
            // Verifica se tem livros vinculados (apenas para categoria, autor tem tabela de ligação)
            if ($tipo === 'categoria') {
                $check = $this->db->query("SELECT count(*) as t FROM livro WHERE categoria = $id")->fetch_assoc();
                if ($check['t'] > 0) return ['success'=>false, 'error'=>'Item possui livros vinculados.'];
            } else {
                // Se for autor, limpa a tabela 'escritor' antes
                $this->db->query("DELETE FROM escritor WHERE autor = $id");
            }

            $this->db->query("DELETE FROM $tabela WHERE $colId = $id");
            return ['success'=>true];
        }
    }
}
?>