<?php 

function bloqueioLogin(){
if ( !isset($_SESSION['logado']) || $_SESSION['logado'] !== true ) {
    header("Location: /The-Books-On-The-Web/public/templates/login/entrada.html");
    exit; 
    }
}

function bloqueioAdimin(){
    // 1. Garante que a sessão foi iniciada antes de ler os dados
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 2. Verifica se está logado
    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        // Caminho absoluto (costuma funcionar bem se a pasta raiz estiver certa)
        header("Location: /The-Books-On-The-Web/public/templates/login/entrada.html");
        exit;
    }
    
    // 3. Verifica se é admin
    if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
        // AQUI ESTÁ O PULO DO GATO: Ajuste este caminho!
        // Se o painel_logado estiver na pasta anterior (biblioteca), use "../"
        header("Location: ../mybooks.php"); // Exemplo: redireciona para a área comum
        exit;
    }
}

?>