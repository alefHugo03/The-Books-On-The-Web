<?php
// 1. INICIE A SESSÃO E CONECTE (Sempre a primeira coisa)
session_start();
require_once '../conection/conectionBD.php'; // (Confira o caminho!)

// 2. O PORTEIRO (VERIFICAÇÃO DUPLA)
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: /ProjetoM2/The-Books-On-The-Web/public/templates/login/entrada.html");
    exit;
}
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: painel_logado.php"); 
    exit;
}

// Pega o ID do admin logado (para checagens de segurança)
$id_admin_logado = $_SESSION['id_user'];
$mensagem_feedback = ""; // Para mostrar sucesso/erro

// 3. SE CHEGOU AQUI, É ADMIN.
//    AGORA, VERIFIQUE SE O ADMIN ESTÁ *FAZENDO* ALGUMA COISA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // AÇÃO 1: CRIAR UM NOVO USUÁRIO
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $data_nascimento = $_POST['data_nascimento'];
        $senha_digitada = $_POST['senha'];
        $tipo = $_POST['tipo']; // O novo campo (admin/cliente)

        // Segurança: Faça o hash da senha
        $hash = password_hash($senha_digitada, PASSWORD_DEFAULT);

        // Insira no banco
        $sql_create = 'INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt_create = mysqli_prepare($con, $sql_create);
        // "ssssss" = 6 strings (incluindo o 'tipo')
        mysqli_stmt_bind_param($stmt_create, 'ssssss', $data_nascimento, $nome, $email, $hash, $cpf, $tipo);
        
        if (mysqli_stmt_execute($stmt_create)) {
            $mensagem_feedback = "Usuário '$nome' criado com sucesso!";
        } else {
             if (mysqli_errno($con) == 1062) {
                $mensagem_feedback = "Erro: Este e-mail ou CPF já está em uso.";
            } else {
                $mensagem_feedback = "Erro ao criar usuário: " . mysqli_error($con);
            }
        }
    }

    // AÇÃO 2: DELETAR UM USUÁRIO
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id_para_deletar = $_POST['id_user_to_delete'];

        // Segurança: Impede o admin de se auto-deletar
        if ($id_para_deletar == $id_admin_logado) {
            $mensagem_feedback = "Erro: Você não pode deletar a si mesmo!";
        } else {
            // Delete do banco
            $sql_delete = "DELETE FROM usuarios WHERE id_user = ?";
            $stmt_delete = mysqli_prepare($con, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, 'i', $id_para_deletar);
            
            if (mysqli_stmt_execute($stmt_delete)) {
                $mensagem_feedback = "Usuário deletado com sucesso!";
            } else {
                $mensagem_feedback = "Erro ao deletar usuário: " . mysqli_error($con);
            }
        }
    }
    
    // (Poderíamos redirecionar, mas mostrar a $mensagem_feedback é mais amigável)
}

// 4. BUSCAR A LISTA DE USUÁRIOS (Sempre roda, depois das ações)
$sql_select = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ?";
$stmt_select = mysqli_prepare($con, $sql_select);
mysqli_stmt_bind_param($stmt_select, "i", $id_admin_logado);
mysqli_stmt_execute($stmt_select);
$resultado_usuarios = mysqli_stmt_get_result($stmt_select);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <base href="http://localhost/ProjetoM2/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <title>Painel Admin - Gerenciar Usuários</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 15px;}
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .form-delete { display: inline; }
        .form-delete button { color: red; background: none; border: none; cursor: pointer; }
        .form-create { background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd; margin-top: 20px; }
        .form-create div { margin-bottom: 10px; }
        .form-create label { display: inline-block; width: 130px; }
        .feedback { padding: 10px; background-color: #e6f7ff; border: 1px solid #b3e0ff; margin-bottom: 15px; }
    </style>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <header>
        <h1>Painel de Controle do Administrador</h1>
        <p>Logado como: <?php echo htmlspecialchars($_SESSION['email_user']); ?> (Admin)</p>
        <nav>
            <a href="src/login/painel_logado.php">Ver Painel (Visão Cliente)</a> |
            <a href="src/login/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        
        <?php if (!empty($mensagem_feedback)): ?>
            <div class="feedback"><?php echo $mensagem_feedback; ?></div>
        <?php endif; ?>

        <div class="form-create">
            <h2>Cadastrar Novo Usuário</h2>
            <form action="src/login/lista_usuarios.php" method="POST">
                <input type="hidden" name="action" value="create">
                
                <div>
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" required>
                </div>
                <div>
                    <label for="data_nascimento">Data Nasc.:</label>
                    <input type="date" id="data_nascimento" name="data_nascimento">
                </div>
                <div>
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <div>
                    <label for="tipo">Tipo de Conta:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="cliente">Cliente</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <button type="submit">Criar Usuário</button>
                </div>
            </form>
        </div>


        <h2>Lista de Usuários do Sistema</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Ações</th> </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($resultado_usuarios) > 0) {
                    while ($usuario = mysqli_fetch_assoc($resultado_usuarios)) {
                        echo '<tr>';
                        echo '<td>' . $usuario['id_user'] . '</td>';
                        echo '<td>' . htmlspecialchars($usuario['nome']) . '</td>';
                        echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
                        echo '<td>' . htmlspecialchars($usuario['tipo']) . '</td>';
                        
                        // NOVO: BOTÃO DELETAR
                        echo '<td>';
                        echo '<form action="src/login/lista_usuarios.php" method="POST" class="form-delete" onsubmit="return confirm(\'Tem certeza que deseja deletar este usuário? Esta ação não pode ser desfeita.\');">';
                        echo '<input type="hidden" name="action" value="delete">';
                        echo '<input type="hidden" name="id_user_to_delete" value="' . $usuario['id_user'] . '">';
                        echo '<button type="submit">Deletar</button>';
                        echo '</form>';
                        echo '</td>';

                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5">Nenhum outro usuário encontrado.</td></tr>';
                }
                // O botão "Voltar" que você tinha estava mal posicionado.
                // A navegação já está no header.
                ?>
            </tbody>
        </table>

    </main>
</body>
<script src="scripts/script.js"></script>
</html>