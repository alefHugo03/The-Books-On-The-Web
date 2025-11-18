<?php
// ARQUIVO: public/api/classes/Admin.php

class Admin {
    private $db; // Variável para guardar a conexão com o banco

    // O Construtor recebe a conexão do banco ao iniciar a classe
    public function __construct($conexao) {
        $this->db = $conexao;
    }

    // 1. Método para Listar Usuários (Ativos ou Inativos)
    public function listarUsuarios($idLogado, $ativo = 1) {
        // Seleciona usuários que NÃO sejam o próprio admin logado
        $sql = "SELECT id_user, nome, email, tipo, cpf, data_nascimento FROM usuarios WHERE id_user != ? AND is_active = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $idLogado, $ativo);
        mysqli_stmt_execute($stmt);
        
        $resultado = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    }

    // 2. Método para Criar Novo Usuário
    public function criarUsuario($dados) {
        // Verifica duplicidade antes de tentar inserir
        if ($this->verificarDuplicidade($dados['email'], $dados['cpf'])) {
            return ['success' => false, 'message' => 'Erro: E-mail ou CPF já cadastrados.'];
        }

        // Criptografa a senha aqui dentro
        $hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        
        // is_active = 1 por padrão
        $sql = "INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($this->db, $sql);
        
        mysqli_stmt_bind_param($stmt, 'ssssss', 
            $dados['data'], 
            $dados['nome'], 
            $dados['email'], 
            $hash, 
            $dados['cpf'], 
            $dados['tipo']
        );

        if (mysqli_stmt_execute($stmt)) {
            return ['success' => true, 'message' => 'Usuário criado com sucesso!'];
        } else {
            return ['success' => false, 'message' => 'Erro ao criar: ' . mysqli_error($this->db)];
        }
    }

    // 3. Método Auxiliar Privado (Só a classe usa) para checar duplicidade
    private function verificarDuplicidade($email, $cpf) {
        $sql = "SELECT id_user FROM usuarios WHERE email = ? OR cpf = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $email, $cpf);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        // Se retornou alguma linha, é porque já existe
        return mysqli_stmt_num_rows($stmt) > 0;
    }

    // 4. Método Genérico para Mudar Status (Ativar ou Desativar)
    public function alterarStatus($id, $novoStatus) {
        $sql = "UPDATE usuarios SET is_active = ? WHERE id_user = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $novoStatus, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $msg = $novoStatus == 1 ? "ativado" : "desativado";
            return ['success' => true, 'message' => "Usuário $msg com sucesso."];
        }
        return ['success' => false, 'message' => "Erro ao alterar status."];
    }

    // 5. Método para Excluir Permanente (Hard Delete)
    public function excluirPermanente($id) {
        $sql = "DELETE FROM usuarios WHERE id_user = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        
        try {
            if (mysqli_stmt_execute($stmt)) {
                return ['success' => true, 'message' => "Usuário excluído permanentemente."];
            }
        } catch (Exception $e) {
            // Captura erro de chave estrangeira (se o usuário tiver livros, etc)
            return ['success' => false, 'message' => "Não é possível excluir: Usuário possui registros vinculados."];
        }
        return ['success' => false, 'message' => "Erro desconhecido ao excluir."];
    }
}
?>