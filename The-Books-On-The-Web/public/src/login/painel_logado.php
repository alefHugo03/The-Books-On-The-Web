<?php
// 1. INICIE A SESSÃO (Sempre a primeira coisa)
session_start();

// 2. O PORTEIRO: (O que já fizemos)
// Verifique se o ingresso 'logado' NÃO existe ou se NÃO é 'true'.
if ( !isset($_SESSION['logado']) || $_SESSION['logado'] !== true ) {
    
    // Chute o usuário para fora
    header("Location: /ProjetoM2/The-Books-On-The-Web/public/templates/login/entrada.html");
    exit; 
}

// 3. A NOVIDADE: BUSCAR OS DADOS NA SESSÃO
// Sabemos que o usuário está logado. Vamos pegar o ID dele na "caixa" da sessão.
$id_do_usuario_logado = $_SESSION['id_user'];


// 4. A NOVIDADE: BUSCAR OS DADOS FRESCOS NO BANCO
// Conecte ao banco (lembre-se de corrigir o caminho se necessário)
require_once '../conection/conectionBD.php'; // Ajuste este caminho!

// Prepare um SQL para buscar TUDO desse usuário (exceto a senha!)
$sql = "SELECT nome, email, data_nascimento, cpf FROM usuarios WHERE id_user = ?";
$stmt = mysqli_prepare($con, $sql);

// "i" significa que a variável $id_do_usuario_logado é um Inteiro
mysqli_stmt_bind_param($stmt, "i", $id_do_usuario_logado);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

// Pegue os dados do usuário e coloque no array $usuario
$usuario = mysqli_fetch_assoc($resultado);

// Se, por algum motivo, o usuário não for encontrado no banco (muito raro)
if (!$usuario) {
    echo "Erro: Usuário não encontrado no banco de dados.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://localhost/ProjetoM2/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg"  type="image/x-icon" class="favicon">
    <title>The Books On The Web</title>
</head>
<body>
    <header id="header-placeholder"></header>
    
    <main class="caixa-menu-login">
        <h1>
            Bem-vindo(a), <?php echo htmlspecialchars($usuario['nome']); ?>!
        </h1>

        <div class="perfil-info">
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
            <p><strong>CPF:</strong> <?php echo htmlspecialchars($usuario['cpf']); ?></p>
            <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($usuario['data_nascimento']); ?></p>
            
            <p><strong>Tipo de Conta:</strong> <?php echo htmlspecialchars($_SESSION['tipo']); ?></p>
        </div>

        <a href="logout.php">Sair (Queimar o Ingresso)</a>

        <?php
        // BÔNUS: Mostrar conteúdo exclusivo do Admin
        if ($_SESSION['tipo'] === 'admin') {
            echo '<button type="button" id="btnVoltar" class="btn-menu"><a href="src\admin\lista_usuarios.php">Usuários</a></button>
';
        }
        ?>

    </main>

    <footer id="footer-placeholder" class="caixa-footer"></footer>
</body>
<script src="scripts/script.js"></script>
</html>