<?php
session_start();
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $cpf = mysqli_real_escape_string($conn, $_POST['cpf']);

    // Validações
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha) || empty($cpf)) {
        $message = "Por favor, preencha todos os campos.";
    } elseif ($senha !== $confirmar_senha) {
        $message = "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        $message = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        // Verifica se o email já está cadastrado
        $sql = "SELECT id_user FROM usuarios WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) > 0) {
            $message = "Este email já está cadastrado.";
        } else {
            // Inicia a transação
            mysqli_begin_transaction($conn);
            
            try {
                // Insere o novo usuário sem endereço
                $sql = "INSERT INTO usuarios (nome, email, senha, cpf, tipo) 
                       VALUES ('$nome', '$email', '$senha', '$cpf', 'cliente')";
                mysqli_query($conn, $sql);
                $novo_id = mysqli_insert_id($conn);

                // Commit da transação
                mysqli_commit($conn);
                
                // Login automático após registro
                $_SESSION['user_id'] = $novo_id;
                $_SESSION['user_nome'] = $nome;
                $_SESSION['user_tipo'] = 'cliente';
                $_SESSION['message'] = "Cadastro realizado com sucesso!";
                header("Location: index.php");
                exit();
                
            } catch (Exception $e) {
                // Se algo der errado, desfaz as alterações
                mysqli_rollback($conn);
                $message = "Erro ao cadastrar: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - The book's on the web</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>The book's on the web</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="form-container">
            <h2>Cadastro</h2>
            <?php if (!empty($message)): ?>
                <div class="alert"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" maxlength="14" placeholder="000.000.000-00" required>
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                    <small>A senha deve ter pelo menos 6 caracteres</small>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                </div>

                <button type="submit" class="btn-primary">Cadastrar</button>
            </form>
            
            <p class="text-center">
                Já tem uma conta? <a href="login.php">Faça login</a>
            </p>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 Book Store Online. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
    // Máscara para CPF
    document.getElementById('cpf').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 11) value = value.substr(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });

    // Máscara para CEP
    document.getElementById('cep').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) value = value.substr(0, 8);
        if (value.length > 5) {
            value = value.substr(0, 5) + '-' + value.substr(5);
        }
        e.target.value = value;
    });

    // Busca endereço pelo CEP usando a API ViaCEP
    document.getElementById('cep').addEventListener('blur', function(e) {
        const cep = e.target.value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('rua').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        
                        // Seleciona o estado
                        const estadoSelect = document.getElementById('estado');
                        for (let i = 0; i < estadoSelect.options.length; i++) {
                            if (estadoSelect.options[i].text.includes(data.uf)) {
                                estadoSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                })
                .catch(error => console.error('Erro ao buscar CEP:', error));
        }
    });
    </script>
</body>
</html>
