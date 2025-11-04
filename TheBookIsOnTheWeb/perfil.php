<?php
session_start();
require_once 'config/db.php';

// Se não estiver logado, redireciona para login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// Buscar endereço do usuário
$stmt = $conn->prepare("SELECT e.* FROM endereco e 
                       INNER JOIN usuarios u ON u.endereco = e.id_endereco 
                       WHERE u.id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$endereco = $stmt->get_result()->fetch_assoc();

// Se o usuário clicou em Sair
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - The book's on the web</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>The book's on the web</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="?logout=1">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="profile-container">
            <h2>Meu Perfil</h2>
            <?php if (!empty($message)): ?>
                <div class="alert"><?php echo $message; ?></div>
            <?php endif; ?>

            <div class="profile-info">
                <h3>Dados Pessoais</h3>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuario['nome']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
                <p><strong>CPF:</strong> <?php echo htmlspecialchars($usuario['cpf']); ?></p>
            </div>
            <br>
            <div class="profile-address">
                <h3>Endereço</h3>
                <?php if ($endereco): ?>
                    <p><strong>CEP:</strong> <?php echo htmlspecialchars($endereco['cep']); ?></p>
                    <p><strong>Rua:</strong> <?php echo htmlspecialchars($endereco['rua']); ?></p>
                    <p><strong>Número:</strong> <?php echo htmlspecialchars($endereco['numero']); ?></p>
                    <?php if (!empty($endereco['complemento'])): ?>
                        <p><strong>Complemento:</strong> <?php echo htmlspecialchars($endereco['complemento']); ?></p>
                    <?php endif; ?>
                    <p><strong>Bairro:</strong> <?php echo htmlspecialchars($endereco['bairro']); ?></p>
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($endereco['cidade']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($endereco['estado']); ?></p>
                <?php else: ?>
                    <p>Nenhum endereço cadastrado.</p>
                <?php endif; ?>
                <br>
                <a href="completar_endereco.php" class="btn-primary"><?php echo $endereco ? 'Editar Endereço' : 'Adicionar Endereço'; ?></a>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 Book Store Online. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>