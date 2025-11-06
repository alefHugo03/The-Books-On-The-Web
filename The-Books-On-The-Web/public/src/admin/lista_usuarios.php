<?php
session_start();
require_once '../conection/conectionBD.php';

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: /ProjetoM2/The-Books-On-The-Web/public/templates/login/entrada.html");
    exit;
}
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: painel_logado.php");
    exit;
}

$id_admin_logado = $_SESSION['id_user'];
$mensagem_feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $data_nascimento = $_POST['data'];
        $senha_digitada = $_POST['senha'];
        $tipo = $_POST['tipo'];

        $hash = password_hash($senha_digitada, PASSWORD_DEFAULT);

        $sql_create = 'INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt_create = mysqli_prepare($con, $sql_create);
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

    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id_para_desativar = $_POST['id_user_to_delete'];

        if ($id_para_desativar == $id_admin_logado) {
            $mensagem_feedback = "Erro: Você não pode desativar a si mesmo!";
        } else {

            $sql_delete = "UPDATE usuarios SET is_active = 0 WHERE id_user = ?";
            $stmt_delete = mysqli_prepare($con, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, 'i', $id_para_desativar);

            if (mysqli_stmt_execute($stmt_delete)) {
                $mensagem_feedback = "Usuário desativado com sucesso!";
            } else {
                $mensagem_feedback = "Erro ao desativar usuário: " . mysqli_error($con);
            }
        }
    }
}

$sql_select = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 1";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon" class="favicon">
    <title>The Books On The Web</title>
</head>

<body>
    <header>
        <div class="cabecalho header-cima">
            <div class="empresa">
                <a href="index.php" class="nome-empresa">
                    <h1>The Books<br> On The Web</h1>
                </a>
                <a href="index.php" class="nome-empresa"><img src="styles/img/favicon.svg" alt="imagem logo" class="imagem-empresa"></a>
            </div>

            <div>
                <h3>Painel de Controle do Administrador</h3>
                <p>Logado como: <?php echo htmlspecialchars($_SESSION['email_user']); ?> (Admin)</p>
                <nav>
                    <a href="src/login/painel_logado.php">Ver Painel</a> |
                    <a href="src/login/logout.php">Sair</a>
                </nav>
            </div>
        </div>
        <div class="cabecalho header-baixo">
            <nav class="opcoes">
                <a href="index.php" class="item-menu">Home</a>
                <a href="templates/biblioteca/resumo.html" class="item-menu">Sobre</a>
                <a href="templates/biblioteca/livros.php" class="item-menu">Serviços</a>

                <?php
                if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
                    echo '<a href="templates/biblioteca/mybooks.php" class="item-menu">Meus Livros</a>';
                }
                ?>
                <?php
                if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') {
                    echo '<a href="src/admin/lista_usuarios.php" class="item-menu">Painel Admin</a>';
                }
                ?>
            </nav>
        </div>
    </header>

    <main class="painel-admin">

        <?php if (!empty($mensagem_feedback)): ?>
            <div class="feedback"><?php echo $mensagem_feedback; ?></div>
        <?php endif; ?>
        
        <h2>Cadastrar Novo Usuário butt <button onclick="alternarDiv()"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 10l5 5 5-5" /></svg></button></h2>
        <div class="form-create conteudo-oculto" id="minhaDiv">
            <form action="src/admin/lista_usuarios.php" method="POST" class="menu">
                <input type="hidden" name="action" value="create">

                <div class="valor caixa-texto">
                    <label for="nome">Nome: </label>
                    <input type="text" name="nome" id="nome" class="valor-texto" placeholder="Digite seu nome" required>
                    <p id="avisoNome" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="email">Email: </label>
                    <input type="email" name="email" class="valor-texto" id="email" placeholder="Digite seu email" required>
                    <p id="avisoEmail" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="cpf">CPF: </label>
                    <input type="text" name="cpf" id="cpf" class="valor-texto" placeholder="Digite seu CPF" required>
                    <p id="avisoCpf" class="aviso"></p>
                </div>




                <div class="valor caixa-texto">
                    <label for="data">Data de Nascimento: </label>
                    <input type="date" name="data" id="data" class="valor-texto" placeholder="Digite sua data de nascimento" required>
                    <p id="avisoData" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="senha">Senha: </label>
                    <input type="password" name="senha" id="senha" class="valor-texto" placeholder="Digite sua senha" required>
                    <p id="avisoSenha" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="senhaDois">Repita a senha: </label>
                    <input type="password" name="senhaDois" id="senhaDois" class="valor-texto" placeholder="Digite novamente sua senha" required>
                    <p id="avisoSenhaDois" class="aviso"></p>
                </div>


                <div>
                    <label for="tipo">Tipo de Conta:</label>
                    <select id="tipo" name="tipo" class="valor-texto" required>
                        <option value="cliente">Cliente</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="interativo">
                        <button type="submit" id="btn-menu-criar" class="btn-menu">Criar</button>
                    </div>
            </form>
        </div>


        <h2>Lista de Usuários Ativos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
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

                        // BOTÃO "DELETAR" (Desativar)
                        echo '<td>';
                        // Este formulário posta para a própria página
                        echo '<form action="src/admin/lista_usuarios.php" method="POST" class="form-delete" onsubmit="return confirm(\'Tem certeza que deseja DESATIVAR este usuário? Ele não poderá mais logar.\');">';
                        echo '<input type="hidden" name="action" value="delete">';
                        echo '<input type="hidden" name="id_user_to_delete" value="' . $usuario['id_user'] . '">';
                        echo '<button type="submit">Desativar</button>';
                        echo '</form>';
                        echo '</td>';

                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5">Nenhum outro usuário ativo encontrado.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </main>
    <footer id="footer-placeholder" class="caixa-footer"></footer>
</body>
<script src="scripts/script.js"></script>
<script src="scripts/animations/ocultar.js"></script>

</html>