<?php
session_start();
require_once '../config/db.php';

// Verificar se está logado e é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];
        
        switch ($_POST['action']) {
            case 'make_admin':
                $stmt = $conn->prepare("UPDATE usuarios SET tipo = 'admin' WHERE id_user = ?");
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    $message = "Usuário promovido a administrador com sucesso!";
                } else {
                    $message = "Erro ao promover usuário: " . $conn->error;
                }
                break;
                
            case 'remove_admin':
                // Não permite remover o próprio acesso admin
                if ($user_id == $_SESSION['user_id']) {
                    $message = "Você não pode remover seu próprio acesso de administrador!";
                    break;
                }
                
                $stmt = $conn->prepare("UPDATE usuarios SET tipo = 'cliente' WHERE id_user = ?");
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    $message = "Privilégios de administrador removidos com sucesso!";
                } else {
                    $message = "Erro ao remover privilégios: " . $conn->error;
                }
                break;
        }
    }
}

// Buscar todos os usuários
$usuarios = [];
$sql = "SELECT id_user, nome, email, tipo, cpf FROM usuarios ORDER BY nome";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $usuarios[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Painel Administrativo</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>Gerenciar Usuários</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="../index.php">Voltar para o Site</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <section class="admin-content">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>CPF</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['cpf']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($usuario['tipo'])); ?></td>
                                <td>
                                    <?php if ($usuario['tipo'] !== 'admin'): ?>
                                        <form method="POST" action="usuarios.php" style="display: inline;">
                                            <input type="hidden" name="action" value="make_admin">
                                            <input type="hidden" name="user_id" value="<?php echo $usuario['id_user']; ?>">
                                            <button type="submit" class="btn-small" onclick="return confirm('Tem certeza que deseja tornar este usuário um administrador?')">Tornar Admin</button>
                                        </form>
                                    <?php else: ?>
                                        <?php if ($usuario['id_user'] != $_SESSION['user_id']): ?>
                                            <form method="POST" action="usuarios.php" style="display: inline;">
                                                <input type="hidden" name="action" value="remove_admin">
                                                <input type="hidden" name="user_id" value="<?php echo $usuario['id_user']; ?>">
                                                <button type="submit" class="btn-small btn-danger" onclick="return confirm('Tem certeza que deseja remover os privilégios de administrador deste usuário?')">Remover Admin</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Você está logado</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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