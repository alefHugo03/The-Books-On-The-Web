<?php
session_start();

// Verifica se está logado
if ( !isset($_SESSION['logado']) || $_SESSION['logado'] !== true ) {
    header("Location: http://192.168.0.136:80/The-Books-On-The-Web/public/templates/login/entrada.html");
    exit; 
}

$id_do_usuario_logado = $_SESSION['id_user'];

require_once(__DIR__ . '/../../api/conection/conectionBD.php');

// Busca dados do usuário
$sql = "SELECT nome, email, data_nascimento, cpf FROM usuarios WHERE id_user = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_do_usuario_logado);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario) {
    echo "Erro: Usuário não encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <base href="http://192.168.0.136:80/The-Books-On-The-Web/public/">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/style.css">
        <link rel="stylesheet" href="styles/cards.css">
        <link rel="stylesheet" href="styles/livros.css">
        
        <link rel="stylesheet" href="styles/stylephone.css?v=<?php echo time(); ?>">
        <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon">
        <title>Painel | TBOTW</title>
    </head>
    <body>
        <header id="header-placeholder"></header>
        
        <main>
            <div class="caixa-menu-login">
                <h2>Bem-vindo(a), <?php echo htmlspecialchars($usuario['nome']); ?>!</h2>

                <div class="perfil-info">
                    <p><strong>E-mail:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                    <p><strong>CPF:</strong> <?php echo htmlspecialchars($usuario['cpf']); ?></p>
                    <p><strong>Data de Nascimento:</strong> <?php echo date('d/m/Y', strtotime($usuario['data_nascimento'])); ?></p>
                    <p><strong>Tipo de Conta:</strong> <?php echo htmlspecialchars($_SESSION['tipo']); ?></p>
                </div>

                <a href="api/login/logout.php" class="btn-menu btn-sair">Sair</a>
            </div>
        </main>

        <footer id="footer-placeholder" class="caixa-footer"></footer>
    </body>
    <script src="scripts/script.js"></script>
</html>