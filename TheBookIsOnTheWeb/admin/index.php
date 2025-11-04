<?php
session_start();
require_once '../config/db.php';

// Verificar se está logado e é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = isset($_SESSION['admin_message']) ? $_SESSION['admin_message'] : '';
unset($_SESSION['admin_message']);

// Buscar estatísticas básicas
$stats = [
    'total_livros' => 0,
    'total_usuarios' => 0
];

// Total de livros
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM livro");
$stats['total_livros'] = mysqli_fetch_assoc($result)['total'];

// Total de usuários
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios");
$stats['total_usuarios'] = mysqli_fetch_assoc($result)['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - The book's on the web</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>Painel Administrativo</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">Voltar para o Site</a></li>
                    <li><a href="livros.php">Gerenciar Livros</a></li>
                    <li><a href="usuarios.php">Gerenciar Usuários</a></li>
                    <li><a href="../perfil.php">Meu Perfil</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="admin-dashboard">
            <h2>Visão Geral</h2>
            
            <div class="admin-stats">
                <div class="stat-card">
                    <h3>Total de Livros</h3>
                    <p class="stat-number"><?php echo $stats['total_livros']; ?></p>
                    <a href="livros.php" class="btn-primary">Gerenciar Livros</a>
                </div>

                <div class="stat-card">
                    <h3>Total de Usuários</h3>
                    <p class="stat-number"><?php echo $stats['total_usuarios']; ?></p>
                    <a href="usuarios.php" class="btn-primary">Gerenciar Usuários</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 Book Store Online. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>