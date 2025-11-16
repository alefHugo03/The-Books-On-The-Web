/* ARQUIVO: public/scripts/biblioteca/admin_functions.js 
   Objetivo: Controlar Modais, AJAX e Edição do Painel Admin sem depender de módulos.
*/

// --- FUNÇÕES GLOBAIS DE LIVRO ---

function editarLivro(livro) {
    const form = document.getElementById('form-cadastro');
    
    // Garante que o form apareça
    form.classList.remove('conteudo-oculto');
    form.style.display = 'block';

    // Preenche os campos
    document.getElementById('action').value = 'edit';
    document.getElementById('livro_id').value = livro.id_livro;
    document.getElementById('titulo').value = livro.titulo;
    document.getElementById('descricao').value = livro.descricao;
    document.getElementById('data_publi').value = livro.data_publi;
    document.getElementById('categoria').value = livro.categoria;
    
    // Preenche o Autor se existir
    if(livro.id_autor) {
        const autorSelect = document.getElementById('autor');
        if(autorSelect) autorSelect.value = livro.id_autor;
    }

    // Atualiza aviso de PDF e Botão
    const existingPdf = document.getElementById('existingPdf');
    if (existingPdf) existingPdf.innerText = livro.pdf ? 'Arquivo atual: ' + livro.pdf : '';

    document.getElementById('btn-menu-criar').innerText = "Atualizar Livro";
    
    // Rola a tela até o formulário
    form.scrollIntoView({ behavior: 'smooth' });
}

// --- CONTROLE DE MODAIS (ABRIR/FECHAR) ---

function showNewCategoryForm() { document.getElementById('novaCategoriaModal').style.display = 'block'; }
function hideNewCategoryForm() { document.getElementById('novaCategoriaModal').style.display = 'none'; }

function showManageCategoryForm() { document.getElementById('gerenciarCategoriaModal').style.display = 'block'; }
function hideManageCategoryForm() { document.getElementById('gerenciarCategoriaModal').style.display = 'none'; }

function showNewAutorForm() { document.getElementById('novoAutorModal').style.display = 'block'; }
function hideNewAutorForm() { document.getElementById('novoAutorModal').style.display = 'none'; }

function showManageAutorForm() { document.getElementById('gerenciarAutorModal').style.display = 'block'; }
function hideManageAutorForm() { document.getElementById('gerenciarAutorModal').style.display = 'none'; }


// --- FUNÇÕES AJAX (SALVAR E EXCLUIR) ---

// Salvar Categoria
function salvarCategoria() {
    enviarDadosAjax('add_categoria', 'nome_categoria_modal', 'Categoria Criada!');
}

// Salvar Autor
function salvarAutor() {
    enviarDadosAjax('add_autor', 'nome_autor_modal', 'Autor Criado!');
}

// Excluir Categoria
function excluirCategoria(id) {
    excluirItemAjax('delete_categoria', 'id_categoria', id, 'Categoria excluída!');
}

// Excluir Autor
function excluirAutor(id) {
    excluirItemAjax('delete_autor', 'id_autor', id, 'Autor excluído!');
}

// --- LÓGICA GENÉRICA DO AJAX ---

function enviarDadosAjax(action, inputId, successMsg) {
    const nomeInput = document.getElementById(inputId);
    const nome = nomeInput.value.trim();
    
    if (!nome) { alert('Insira um nome.'); return; }

    const formData = new FormData();
    formData.append('action', action);
    
    if (action === 'add_autor') formData.append('nome_autor', nome);
    else formData.append('nome_categoria', nome);

    // Tenta enviar para o endpoint (o próprio arquivo PHP)
    // Usamos window.location.href para garantir que vá para a página atual
    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(successMsg);
            window.location.reload();
        } else {
            alert('Erro: ' + (data.error || 'Desconhecido'));
        }
    })
    .catch(err => {
        console.error("Erro Ajax:", err);
        alert("Erro de conexão ao tentar salvar.");
    });
}

function excluirItemAjax(action, idKey, idVal, successMsg) {
    if (!confirm("Tem certeza que deseja excluir?")) return;

    const formData = new FormData();
    formData.append('action', action);
    formData.append(idKey, idVal);

    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(successMsg);
            window.location.reload();
        } else {
            alert('Erro: ' + (data.error || 'Desconhecido'));
        }
    })
    .catch(err => {
        console.error("Erro Ajax:", err);
        alert("Erro de conexão ao tentar excluir.");
    });
}

// Fecha modais ao clicar fora da caixa branca
window.onclick = function(event) {
    const modals = [
        document.getElementById('novaCategoriaModal'),
        document.getElementById('gerenciarCategoriaModal'),
        document.getElementById('novoAutorModal'),
        document.getElementById('gerenciarAutorModal')
    ];
    
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// RESET do formulário ao clicar no botão "+" principal
document.addEventListener("DOMContentLoaded", function() {
    const btnToggle = document.getElementById('btn-toggle-cadastro');
    if (btnToggle) {
        btnToggle.addEventListener('click', function() {
            const form = document.getElementById('form-cadastro');
            // Alterna visibilidade
            if(form.classList.contains('conteudo-oculto') || form.style.display === 'none') {
                form.classList.remove('conteudo-oculto');
                form.style.display = 'block';
                
                // Limpa campos para novo cadastro
                document.getElementById('action').value = 'add';
                document.getElementById('livro_id').value = '';
                form.reset();
                document.getElementById('btn-menu-criar').innerText = "Salvar Livro";
                
                const existingPdf = document.getElementById('existingPdf');
                if(existingPdf) existingPdf.innerText = '';
            } else {
                form.classList.add('conteudo-oculto');
                setTimeout(() => { form.style.display = 'none'; }, 300); // Espera animação se houver
            }
        });
    }
});