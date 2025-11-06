<?php
// 1. Inicie a sessão (para poder encontrar a "caixa" do usuário)
session_start();

// 2. Limpe todas as variáveis da sessão (esvazie a caixa)
// Isso apaga $_SESSION['logado'], $_SESSION['id_user'], etc.
session_unset();

// 3. Destrua a sessão (jogue a "caixa" fora)
session_destroy();

// 4. Mande o usuário de volta para a porta de entrada (login)
// (Verifique se este caminho está 100% correto para o seu projeto)
header("Location: /ProjetoM2/The-Books-On-The-Web/public/templates/login/entrada.html");
exit; // Pare o script imediatamente.
?>