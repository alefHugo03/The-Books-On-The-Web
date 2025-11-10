<?php
// require_once '../../../api/admin/validarAdmin.php'; 

// $id_admin_logado = validarAdmin();

// require_once '../../../api/conection/conectionBD.php';

// $mensagem_feedback = "";

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {

//     // AÇÃO 1: CRIAR UM NOVO USUÁRIO
//     if (isset($_POST['action']) && $_POST['action'] === 'create') {
//         $nome = $_POST['nome'];
//         $email = $_POST['email'];
//         $cpf = $_POST['cpf'];
//         // CORREÇÃO: O 'name' do seu formulário é 'data', mas a coluna é 'data_nascimento'
//         // Mudei o formulário (lá embaixo) para enviar 'data_nascimento'
//         $data_nascimento = $_POST['data_nascimento']; 
//         $senha_digitada = $_POST['senha'];
//         $tipo = $_POST['tipo'];

//         $hash = password_hash($senha_digitada, PASSWORD_DEFAULT);

//         $sql_create = 'INSERT INTO usuarios (data_nascimento, nome, email, senha, cpf, tipo) VALUES (?, ?, ?, ?, ?, ?)';
//         $stmt_create = mysqli_prepare($con, $sql_create);
//         mysqli_stmt_bind_param($stmt_create, 'ssssss', $data_nascimento, $nome, $email, $hash, $cpf, $tipo);

//         if (mysqli_stmt_execute($stmt_create)) {
//             $mensagem_feedback = "Usuário '$nome' criado com sucesso!";
//         } else {
//              if (mysqli_errno($con) == 1062) {
//                 $mensagem_feedback = "Erro: Este e-mail ou CPF já está em uso.";
//             } else {
//                 $mensagem_feedback = "Erro ao criar usuário: " . mysqli_error($con);
//             }
//         }
//     }

//     // AÇÃO 2: "DELETAR" (DESATIVAR) UM USUÁRIO - (SOFT DELETE)
//     // (O 'action' que você chamou de 'delete')
//     if (isset($_POST['action']) && $_POST['action'] === 'delete') {
//         $id_para_desativar = $_POST['id_user_to_delete'];

//         if ($id_para_desativar == $id_admin_logado) {
//             $mensagem_feedback = "Erro: Você não pode desativar a si mesmo!";
//         } else {
//             $sql_delete = "UPDATE usuarios SET is_active = 0 WHERE id_user = ?";
//             $stmt_delete = mysqli_prepare($con, $sql_delete);
//             mysqli_stmt_bind_param($stmt_delete, 'i', $id_para_desativar);

//             if (mysqli_stmt_execute($stmt_delete)) {
//                 $mensagem_feedback = "Usuário desativado com sucesso!";
//             } else {
//                 $mensagem_feedback = "Erro ao desativar usuário: " . mysqli_error($con);
//             }
//         }
//     }

//     // AÇÃO 3: ATIVAR UM USUÁRIO (NOVO)
//     if (isset($_POST['action']) && $_POST['action'] === 'activate') {
//         $id_para_ativar = $_POST['id_user_to_activate'];

//         $sql_activate = "UPDATE usuarios SET is_active = 1 WHERE id_user = ?";
//         $stmt_activate = mysqli_prepare($con, $sql_activate);
//         mysqli_stmt_bind_param($stmt_activate, 'i', $id_para_ativar);

//         if (mysqli_stmt_execute($stmt_activate)) {
//             $mensagem_feedback = "Usuário reativado com sucesso!";
//         } else {
//             $mensagem_feedback = "Erro ao reativar usuário: " . mysqli_error($con);
//         }
//     }

//     // AÇÃO 4: DELETAR PERMANENTEMENTE (NOVO)
//     // (Isso só vai funcionar se você usou o SQL com ON DELETE CASCADE)
//     if (isset($_POST['action']) && $_POST['action'] === 'delete_permanent') {
//         $id_para_deletar = $_POST['id_user_to_delete'];

//         if ($id_para_deletar == $id_admin_logado) {
//             $mensagem_feedback = "Erro: Você não pode deletar a si mesmo!";
//         } else {
//             $sql_perm_delete = "DELETE FROM usuarios WHERE id_user = ?";
//             $stmt_perm_delete = mysqli_prepare($con, $sql_perm_delete);
//             mysqli_stmt_bind_param($stmt_perm_delete, 'i', $id_para_deletar);

//             if (mysqli_stmt_execute($stmt_perm_delete)) {
//                 $mensagem_feedback = "Usuário DELETADO PERMANENTEMENTE!";
//             } else {
//                 // Se você não usou o ON DELETE CASCADE, este erro vai aparecer
//                 $mensagem_feedback = "Erro ao deletar: " . mysqli_error($con);
//             }
//         }
//     }
// }

// // 4. LER OS USUÁRIOS (SEPARADAMENTE)
// // Lista de ATIVOS
// $sql_select_ativos = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 1";
// $stmt_select_ativos = mysqli_prepare($con, $sql_select_ativos);
// mysqli_stmt_bind_param($stmt_select_ativos, "i", $id_admin_logado);
// mysqli_stmt_execute($stmt_select_ativos);
// $resultado_usuarios_ativos = mysqli_stmt_get_result($stmt_select_ativos);

// // Lista de INATIVOS
// $sql_select_of = "SELECT id_user, nome, email, tipo FROM usuarios WHERE id_user != ? AND is_active = 0";
// $stmt_select_of = mysqli_prepare($con, $sql_select_of);
// mysqli_stmt_bind_param($stmt_select_of, "i", $id_admin_logado);
// mysqli_stmt_execute($stmt_select_of);
// $resultado_usuarios_desligados = mysqli_stmt_get_result($stmt_select_of);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon" class="favicon">
    <title>The Books On The Web - Admin</title>
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
                <nav style="margin-right: auto;">
                    <a href="templates/login/painel_logado.php">Ver Painel</a> |
                    <a href="api/login/logout.php">Sair</a>
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

                if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin') {
                    echo '<a href="api/admin/lista_usuarios.php" class="item-menu">Painel Admin</a>';
                }
                ?>
            </nav>
        </div>
    </header>

    <main class="painel-admin">

        <?php if (!empty($mensagem_feedback)): ?>
            <div class="feedback"><?php echo $mensagem_feedback; ?></div>
        <?php endif; ?>

        <h2>Cadastrar Novo Usuário <button id="btn-toggle-cadastro"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 10l5 5 5-5" />
                </svg></button></h2>

        <div class="form-create">
            <form method="post" class="menu conteudo-oculto" id="form-cadastro">
                <div class="valor caixa-texto">
                    <label for="nomeAdmin">Nome: </label>
                    <input type="text" name="nomeAdmin" id="nomeAdmin" class="valor-texto" placeholder="Digite seu nome">
                    <p id="avisoNome" class="aviso"></p>
                </div>
                <div class="valor caixa-texto">
                    <label for="emailAdmin">E-mail: </label>
                    <input type="emailAdmin" name="emailAdmin" class="valor-texto" id="emailAdmin" placeholder="Digite seu email">
                    <p id="avisoEmail" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="cpfAdmin">CPF: </label>
                    <input type="text" name="cpfAdmin" id="cpfAdmin" class="valor-texto" oninput="" maxlength="14" placeholder="Digite seu CPF">
                    <p id="avisoCpf" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="dataAdmin">Data de Nascimento: </label>
                    <input type="date" name="dataAdmin" id="dataAdmin" class="valor-texto" placeholder="Digite sua data de nascimento">
                    <p id="avisoData" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="senhaAdmin">Senha: </label>
                    <input type="password" name="senhaAdmin" id="senhaAdmin" class="valor-texto" placeholder="Digite sua senha">
                    <p id="avisoSenha" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="senhaAdminDois">Repita a senha: </label>
                    <input type="password" name="senhaAdminDois" id="senhaAdminDois" class="valor-texto" placeholder="Digite novamente sua senha">
                    <p id="avisoSenhaDois" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="tipo">Tipo de Conta: </label>
                    <select id="tipo" name="tipo" class="valor-texto">
                        <option value="" selected disabled>Selecione</option>
                        <option value="cliente">Cliente</option>
                        <option value="admin">Admin</option>
                    </select>
                    <p id="avisoTipo" class="aviso"></p>
                </div>


                <div class="valor">
                    <div class="interativo">
                        <button type="submit" id="btn-menu-criar" class="btn-menu">Criar</button>
                    </div>
                    <p id="aviso"></p>
                </div>
            </form>
        </div>




        <!-- <div>
            <h2>Lista de Usuários Ativos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Editar</th>
                        <th>Desativar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($resultado_usuarios_ativos) > 0) {
                        while ($usuario = mysqli_fetch_assoc($resultado_usuarios_ativos)) {
                            echo '<tr>';
                            echo '<td>' . $usuario['id_user'] . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['nome']) . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['tipo']) . '</td>';

                            // Botão Editar
                            echo '<td>';
                            echo '<a href="api/admin/editar_usuario.php?id=' . $usuario['id_user'] . '" class="btn-menu btn-edit">Editar</a>';
                            echo '</td>';

                            // Botão Desativar (o seu 'action=delete')
                            echo '<td>';
                            echo '<form action="api/admin/lista_usuarios.php" method="POST" class="form-delete" onsubmit="return confirm(\'Tem certeza que deseja DESATIVAR este usuário?\');">';
                            echo '<input type="hidden" name="action" value="delete">';
                            echo '<input type="hidden" name="id_user_to_delete" value="' . $usuario['id_user'] . '">';
                            echo '<button type="submit">Desativar</button>';
                            echo '</form>';
                            echo '</td>';

                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">Nenhum usuário ativo encontrado.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 40px;">
            <h2>Lista de Usuários Inativos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Ativar</th>
                        <th>Deletar (Perm.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($resultado_usuarios_desligados) > 0) {
                        while ($usuario = mysqli_fetch_assoc($resultado_usuarios_desligados)) {
                            echo '<tr>';
                            echo '<td>' . $usuario['id_user'] . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['nome']) . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($usuario['tipo']) . '</td>';

                            // Botão Ativar
                            echo '<td>';
                            echo '<form action="api/admin/lista_usuarios.php" method="POST" class="form-activate">';
                            echo '<input type="hidden" name="action" value="activate">';
                            echo '<input type="hidden" name="id_user_to_activate" value="' . $usuario['id_user'] . '">';
                            echo '<button type="submit" class="btn-menu btn-success">Ativar</Gbutton>';
                            echo '</form>';
                            echo '</td>';

                            // Botão Deletar Permanente
                            echo '<td>';
                            echo '<form action="api/admin/lista_usuarios.php" method="POST" class="form-delete" onsubmit="return confirm(\'DELETAR PERMANENTEMENTE? Esta ação é irreversível e pode falhar se o ON DELETE CASCADE não estiver ativo.\');">';
                            echo '<input type="hidden" name="action" value="delete_permanent">';
                            echo '<input type="hidden" name="id_user_to_delete" value="' . $usuario['id_user'] . '">';
                            echo '<button type="submit" class="btn-menu">Deletar 100%</button>';
                            echo '</form>';
                            echo '</td>';

                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">Nenhum usuário inativo encontrado.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div> -->

    </main>
    <footer id="footer-placeholder" class="caixa-footer"></footer>
</body>
<script src="scripts/script.js"></script>
<script type="module" src="scripts/login/cadastroAdmin.js"></script>
<script type="module" src="scripts/animations/ocultar.js"></script>

</html>