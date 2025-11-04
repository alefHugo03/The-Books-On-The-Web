<?php
session_start();
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = $_POST['senha'];

    if (empty($email) || empty($senha)) {
        $message = "Por favor, preencha todos os campos.";
    } else {
        $sql = "SELECT id_user, nome, senha, tipo FROM usuarios WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $usuario = mysqli_fetch_assoc($result);
            
            if ($senha == $usuario['senha']) {
                $_SESSION['user_id'] = $usuario['id_user'];
                $_SESSION['user_nome'] = $usuario['nome'];
                $_SESSION['user_tipo'] = $usuario['tipo'];
                header("Location: index.php");
                exit();
            } else {
                $message = "Senha incorreta.";
            }
        } else {
            $message = "Usuário não encontrado.";
        }
    }
}

// Mensagem de sucesso do registro
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - The book's on the web</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>The book's on the web</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="register.php">Cadastro</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="form-container">
            <h2>Login</h2>
            <?php if (!empty($message)): ?>
                <div class="alert"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>

                <button type="submit" class="btn-primary">Entrar</button>
            </form>

            <p class="text-center">
                Não tem uma conta? <a href="register.php">Cadastre-se</a>
            </p>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 Book Store Online. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
