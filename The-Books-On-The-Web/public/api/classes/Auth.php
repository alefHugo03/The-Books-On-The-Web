<?php
// ARQUIVO: public/api/classes/Auth.php

class Auth {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    // --- 1. MÉTODO DE LOGIN ---
    public function login($email, $senha) {
        $sql = "SELECT id_user, nome, senha, tipo FROM usuarios WHERE email = ? AND is_active = 1 LIMIT 1";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        $usuario = mysqli_fetch_assoc($resultado);

        // Verifica se achou e se a senha bate
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Inicia a sessão
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            
            $_SESSION['logado'] = true;
            $_SESSION['id_user'] = $usuario['id_user'];
            $_SESSION['nome_user'] = $usuario['nome'];
            $_SESSION['email_user'] = $email;
            $_SESSION['tipo'] = $usuario['tipo'];

            return [
                'sucesso' => true,
                'mensagem' => "Login bem-sucedido!",
                'redirect_url' => "templates/login/painel_logado.php"
            ];
        } else {
            return [
                'sucesso' => false,
                'mensagem' => "E-mail ou senha inválidos."
            ];
        }
    }

    // --- 2. MÉTODO DE CADASTRO (Genérico) ---
    public function cadastrar($dados, $tipo = 'cliente') {
        // Verifica duplicidade
        if ($this->verificarDuplicidade($dados['email'], $dados['cpf'])) {
            return ['sucesso' => false, 'mensagem' => "E-mail ou CPF já cadastrados."];
        }

        $hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($this->db, $sql);
        
        mysqli_stmt_bind_param($stmt, 'ssssss', 
            $dados['data'], 
            $dados['nome'], 
            $dados['email'], 
            $hash, 
            $dados['cpf'], 
            $tipo // 'cliente' ou 'admin'
        );

        if (mysqli_stmt_execute($stmt)) {
            return [
                'sucesso' => true, 
                'mensagem' => "Cadastro realizado com sucesso!",
                'redirect_url' => "templates/login/entrada.html"
            ];
        } else {
            return ['sucesso' => false, 'mensagem' => "Erro ao cadastrar: " . mysqli_error($this->db)];
        }
    }

    // Auxiliar privado
    private function verificarDuplicidade($email, $cpf) {
        $sql = "SELECT id_user FROM usuarios WHERE email = ? OR cpf = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $email, $cpf);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        return mysqli_stmt_num_rows($stmt) > 0;
    }
}
?>