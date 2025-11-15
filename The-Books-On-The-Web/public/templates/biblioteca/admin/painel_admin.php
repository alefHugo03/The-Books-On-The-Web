<?php
require_once '../../../api/conection/bloqueioLogin.php';
bloqueioAdimin();
// Removido conectionBD.php daqui, pois a API que faz a conexão agora.
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Gerenciar Usuários</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/livros.css">
    <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon">
</head>

<body>
    <header id="header-placeholder"></header>

    <main class="painel-admin">
        <div id="feedback-msg" class="feedback" style="display:none;"></div>

        <h2>Cadastrar Usuário
            <button id="btn-toggle-cadastro" type="button">+</button>
        </h2>

        <div class="form-create">
            <form class="menu conteudo-oculto" id="form-cadastro">
                <input type="hidden" name="action" value="create">

                <div class="valor caixa-texto">
                    <label for="nomeAdmin">Nome: </label>
                    <input type="text" name="nomeAdmin" id="nomeAdmin" class="valor-texto" placeholder="Digite seu nome">
                    <p id="avisoNome" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="emailAdmin">E-mail: </label>
                    <input type="email" name="emailAdmin" id="emailAdmin" class="valor-texto" placeholder="Digite seu email">
                    <p id="avisoEmail" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="cpfAdmin">CPF: </label>
                    <input type="text" name="cpfAdmin" id="cpfAdmin" class="valor-texto" maxlength="14" placeholder="Digite seu CPF">
                    <p id="avisoCpf" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="dataAdmin">Data de Nascimento: </label>
                    <input type="date" name="dataAdmin" id="dataAdmin" class="valor-texto">
                    <p id="avisoData" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="senhaAdmin">Senha: </label>
                    <input type="password" name="senhaAdmin" id="senhaAdmin" class="valor-texto" placeholder="Digite sua senha">
                    <p id="avisoSenha" class="aviso"></p>
                </div>

                <div class="valor caixa-texto">
                    <label for="senhaAdminDois">Repita a senha: </label>
                    <input type="password" name="senhaAdminDois" id="senhaAdminDois" class="valor-texto" placeholder="Confirme a senha">
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
                    <div class="botoes-rodape">
                        <button type="reset" class="btn-menu">Limpar</button>
                        <button type="submit" class="btn-menu btn-primary">Criar</button>
                    </div>
                    <p id="aviso" class="aviso"></p>
                </div>
            </form>
        </div>

        <div>
            <h2>Usuários Ativos</h2>
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
                <tbody id="tbody-ativos"></tbody>
            </table>
        </div>

        <div>
            <h2>Usuários Inativos</h2>
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
                <tbody id="tbody-inativos"></tbody>
            </table>
        </div>
    </main>

<script type="module" src="scripts/login/tabela_user.js"></script>
    <script src="scripts/animations/ocultar.js"></script>
    <script src="scripts/script.js"></script>
</body>

</html>