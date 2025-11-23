import GerenciadorLivros from "../classes/GerenciadorLivros.js";

// Função principal que liga tudo
function iniciarGerenciador() {
    console.log("🚀 Livros.js: Inicializando sistema...");

    // 1. Instancia a Classe Principal
    const gerenciador = new GerenciadorLivros({
        apiUrl: "/The-Books-On-The-Web/public/api/biblioteca/gerenciarLivros.php",
        formId: 'form-cadastro',
        tabelaCorpoId: 'tabela-livros-corpo',
        paginacaoId: 'paginacaoContainer',
        buscaInputId: 'buscaLivroInput',
        itensPorPaginaId: 'itensPorPagina',
        btnToggleId: 'btn-toggle-cadastro'
    });

    // 2. Funções Globais para Modais (Abertura/Fechamento)
    // Categoria
    window.showNewCategoryForm = () => document.getElementById('novaCategoriaModal').style.display = 'block';
    window.hideNewCategoryForm = () => document.getElementById('novaCategoriaModal').style.display = 'none';
    window.showManageCategoryForm = () => document.getElementById('gerenciarCategoriaModal').style.display = 'block';
    window.hideManageCategoryForm = () => document.getElementById('gerenciarCategoriaModal').style.display = 'none';

    // Autor
    window.showNewAutorForm = () => document.getElementById('novoAutorModal').style.display = 'block';
    window.hideNewAutorForm = () => document.getElementById('novoAutorModal').style.display = 'none';
    window.showManageAutorForm = () => document.getElementById('gerenciarAutorModal').style.display = 'block';
    window.hideManageAutorForm = () => document.getElementById('gerenciarAutorModal').style.display = 'none';

    // Editora
    window.showNewEditoraForm = () => document.getElementById('novaEditoraModal').style.display = 'block';
    window.hideNewEditoraForm = () => document.getElementById('novaEditoraModal').style.display = 'none';
    window.showManageEditoraForm = () => document.getElementById('gerenciarEditoraModal').style.display = 'block';
    window.hideManageEditoraForm = () => document.getElementById('gerenciarEditoraModal').style.display = 'none';

    // 3. Funções Globais para SALVAR (Auxiliares)
    window.salvarCategoria = () => salvarAuxiliar('add_categoria', 'nome_categoria_modal', 'nome_categoria');
    window.salvarAutor = () => salvarAuxiliar('add_autor', 'nome_autor_modal', 'nome_autor');
    window.salvarEditora = () => salvarAuxiliar('add_editora', 'nome_editora_modal', 'nome_editora');

    function salvarAuxiliar(action, inputId, keyPost) {
        const input = document.getElementById(inputId);
        const valor = input.value.trim();
        
        if(!valor) { alert("Por favor, digite um nome."); return; }
        
        const fd = new FormData();
        fd.append('action', action);
        fd.append(keyPost, valor);
        
        fetch(gerenciador.apiUrl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                alert("Cadastro realizado com sucesso!");
                input.value = '';
                gerenciador.carregarDados(); // Recarrega selects e tabelas
                // Fecha todos os modais abertos
                document.querySelectorAll('.modal').forEach(m => m.style.display='none');
            } else {
                alert("Erro ao salvar: " + data.error);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Erro de conexão ao tentar salvar.");
        });
    }
    
    // 4. Função Global para EXCLUIR (Auxiliares)
    window.deletarAuxiliar = function(action, id) {
        if(!confirm("Tem certeza que deseja excluir este item?")) return;
        
        const fd = new FormData();
        fd.append('action', action);
        
        // Identifica qual ID enviar baseado na ação
        if(action.includes('categoria')) fd.append('id_categoria', id);
        else if(action.includes('autor')) fd.append('id_autor', id);
        else if(action.includes('editora')) fd.append('id_editora', id);

        fetch(gerenciador.apiUrl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                alert("Item excluído!");
                gerenciador.carregarDados();
            } else {
                alert("Erro ao excluir: " + data.error);
            }
        })
        .catch(err => console.error(err));
    };

    // 5. Fecha modais ao clicar fora da caixa branca
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }

    // INICIA TUDO
    gerenciador.init();
}

// --- Inicialização Segura do Módulo ---
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', iniciarGerenciador);
} else {
    iniciarGerenciador();
}