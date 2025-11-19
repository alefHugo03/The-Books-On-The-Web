<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../../login/painel_logado.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <base href="http://localhost/The-Books-On-The-Web/public/">
    <meta charset="UTF-8">
    <title>Gerenciar Livros | Admin</title>
    
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/myAdmin.css"> <link rel="shortcut icon" href="styles/img/favicon.svg" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .close-modal { float: right; font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; }
        .close-modal:hover { color: #000; }
    </style>
</head>
<body>
    <header id="header-placeholder"></header>

    <main class="painel-admin">
        <h2>Adicionar/Editar Livro <button id="btn-toggle-cadastro" type="button">+</button></h2>

        <form class="form-create menu conteudo-oculto" id="form-cadastro" enctype="multipart/form-data">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="livro_id" id="livro_id" value="">

            <div class="valor caixa-texto">
                <label>Título:</label>
                <input type="text" name="titulo" id="titulo" class="valor-texto">
                <p id="avisoTitulo" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>Descrição:</label>
                <textarea name="descricao" id="descricao" class="valor-texto" rows="3"></textarea>
                <p id="avisoDescricao" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>Autores:</label>
                <div style="display:flex; gap:5px;">
                    <select name="autor[]" id="autor" multiple placeholder="Selecione...">
                        <option value="">Carregando...</option>
                    </select>
                    <div class="acoes-input">
                        <button type="button" onclick="showNewAutorForm()" class="btn-small" style="background:#17a2b8;">+</button>
                        <button type="button" onclick="showManageAutorForm()" class="btn-small" style="background:#6c757d;">⚙️</button>
                    </div>
                </div>
                <p id="avisoAutor" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>Editora:</label>
                <div style="display:flex; gap:5px;">
                    <select name="editora" id="editora" placeholder="Selecione...">
                        <option value="">Carregando...</option>
                    </select>
                    <div class="acoes-input">
                        <button type="button" onclick="showNewEditoraForm()" class="btn-small" style="background:#17a2b8;">+</button>
                        <button type="button" onclick="showManageEditoraForm()" class="btn-small" style="background:#6c757d;">⚙️</button>
                    </div>
                </div>
            </div>

            <div class="valor caixa-texto">
                <label>Categorias (Temas):</label>
                <div style="display:flex; gap:5px;">
                    <select name="categoria[]" id="categoria" multiple placeholder="Selecione...">
                        <option value="">Carregando...</option>
                    </select>
                    <div class="acoes-input">
                        <button type="button" onclick="showNewCategoryForm()" class="btn-small" style="background:#17a2b8;">+</button>
                        <button type="button" onclick="showManageCategoryForm()" class="btn-small" style="background:#6c757d;">⚙️</button>
                    </div>
                </div>
                <p id="avisoCategoria" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>Data Publicação:</label>
                <input type="date" name="data_publi" id="data_publi" class="valor-texto">
                <p id="avisoDataPubli" class="aviso"></p>
            </div>

            <div class="valor caixa-texto">
                <label>PDF:</label>
                <input type="file" name="pdf_file" id="pdf_file" class="valor-texto" accept=".pdf">
                <small id="existingPdf" style="color:#666; display:block; margin-top:5px;"></small>
                <p id="avisoPdf" class="aviso"></p>
            </div>

            <div class="interativo">
                <button type="submit" id="btn-menu-criar" class="btn-menu btn-primary">Salvar Livro</button>
            </div>
        </form>

        <hr style="margin:20px 0;">

        <div class="tabela-controles">
            <input type="text" id="buscaLivroInput" class="input-busca-admin" placeholder="🔍 Pesquisar livro, autor, editora...">
            
            <div class="controles-direita">
                <span id="contador-livros" class="badge-total">Total: 0</span>
                <div class="grupo-exibir">
                    <label for="itensPorPagina">Exibir:</label>
                    <select id="itensPorPagina">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor(es)</th>
                        <th>Editora</th>
                        <th>Categorias</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-livros-corpo">
                    <tr><td colspan="5" align="center">Carregando dados...</td></tr>
                </tbody>
            </table>
        </div>

        <div id="paginacaoContainer" class="paginacao-admin"></div>

        <div id="novaCategoriaModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span onclick="hideNewCategoryForm()" class="close-modal">&times;</span>
                <h3>Nova Categoria</h3>
                <input type="text" id="nome_categoria_modal" class="valor-texto" placeholder="Nome da Categoria" style="margin: 15px 0; width: 100%;">
                <button onclick="salvarCategoria()" class="btn-small btn-ativar" style="width: 100%;">Salvar</button>
            </div>
        </div>
        <div id="gerenciarCategoriaModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span onclick="hideManageCategoryForm()" class="close-modal">&times;</span>
                <h3>Gerenciar Categorias</h3>
                <table style="width:100%; margin-top:10px;"><tbody id="lista-categorias-modal"></tbody></table>
            </div>
        </div>

        <div id="novoAutorModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span onclick="hideNewAutorForm()" class="close-modal">&times;</span>
                <h3>Novo Autor</h3>
                <input type="text" id="nome_autor_modal" class="valor-texto" placeholder="Nome do Autor" style="margin: 15px 0; width: 100%;">
                <button onclick="salvarAutor()" class="btn-small btn-ativar" style="width: 100%;">Salvar</button>
            </div>
        </div>
        <div id="gerenciarAutorModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span onclick="hideManageAutorForm()" class="close-modal">&times;</span>
                <h3>Gerenciar Autores</h3>
                <table style="width:100%; margin-top:10px;"><tbody id="lista-autores-modal"></tbody></table>
            </div>
        </div>

        <div id="novaEditoraModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span onclick="hideNewEditoraForm()" class="close-modal">&times;</span>
                <h3>Nova Editora</h3>
                <input type="text" id="nome_editora_modal" class="valor-texto" placeholder="Nome da Editora" style="margin: 15px 0; width: 100%;">
                <button onclick="salvarEditora()" class="btn-small btn-ativar" style="width: 100%;">Salvar</button>
            </div>
        </div>
        <div id="gerenciarEditoraModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span onclick="hideManageEditoraForm()" class="close-modal">&times;</span>
                <h3>Gerenciar Editoras</h3>
                <table style="width:100%; margin-top:10px;"><tbody id="lista-editoras-modal"></tbody></table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="/The-Books-On-The-Web/public/scripts/script.js"></script>
    <script type="module" src="/The-Books-On-The-Web/public/scripts/biblioteca/livros.js?v=<?php echo time(); ?>"></script>
</body>
</html>