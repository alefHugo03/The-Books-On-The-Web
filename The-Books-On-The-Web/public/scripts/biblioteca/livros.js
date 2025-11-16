import { 
    validarTitulo, 
    validarDescricao, 
    validarDataPublicacao, 
    validarCategoria, 
    validarPdf 
} from "../validations/validarLivros.js"; 

import { etapa, limparAviso } from "../validations/utilits.js";

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. MENSAGEM DE FEEDBACK (Some após 3 segundos)
    const feedbackMsg = document.querySelector('.feedback');
    if (feedbackMsg) {
        setTimeout(() => {
            feedbackMsg.style.transition = "opacity 0.5s ease";
            feedbackMsg.style.opacity = "0";
            setTimeout(() => { feedbackMsg.remove(); }, 500); 
        }, 3000);
    }

    // 2. FORMULÁRIO DE LIVROS
    const formLivro = document.getElementById('form-cadastro');
    if (formLivro) {
        formLivro.addEventListener('submit', function(event) {
            event.preventDefault(); 
            
            // Limpa avisos (Incluindo o novo avisoAutor)
            const avisosLivro = ["avisoTitulo", "avisoDescricao", "avisoDataPubli", "avisoCategoria", "avisoAutor", "avisoPdf"];
            avisosLivro.forEach(id => limparAviso(id));

            const actionInput = document.getElementById('action');
            const isEditMode = actionInput && actionInput.value === 'edit';

            // Validações
            const titulo = validarTitulo('titulo', 'avisoTitulo');
            const descricao = validarDescricao('descricao', 'avisoDescricao');
            const dataPubli = validarDataPublicacao('data_publi', 'avisoDataPubli');
            const categoria = validarCategoria('categoria', 'avisoCategoria');
            // Reutilizamos validarCategoria para o Autor, pois é um <select> igual
            const autor = validarCategoria('autor', 'avisoAutor'); 
            const pdf = validarPdf('pdf_file', 'avisoPdf', !isEditMode); 

            if (titulo && descricao && dataPubli && categoria && autor && pdf) {
                formLivro.submit();
            }
        });
    }

    // 3. AUTO-PREENCHER TÍTULO
    const pdfInput = document.getElementById('pdf_file');
    if (pdfInput) {
        pdfInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const name = this.files[0].name.replace(/\.[^/.]+$/, '');
                const tituloEl = document.getElementById('titulo');
                if (tituloEl && tituloEl.value.trim() === '') {
                    tituloEl.value = name;
                }
            }
        });
    }
    
    // 4. RESETAR AO CLICAR NO "+"
    const btnToggle = document.getElementById('btn-toggle-cadastro');
    if (btnToggle && formLivro) {
        btnToggle.addEventListener('click', function() {
            document.getElementById('action').value = 'add';
            document.getElementById('livro_id').value = '';
            formLivro.reset();
            
            const avisosLivro = ["avisoTitulo", "avisoDescricao", "avisoDataPubli", "avisoCategoria", "avisoAutor", "avisoPdf"];
            avisosLivro.forEach(id => limparAviso(id));

            const pdfMsg = document.getElementById('existingPdf');
            if(pdfMsg) pdfMsg.innerText = '';
            
            const btnSalvar = document.getElementById('btn-menu-criar');
            if(btnSalvar) btnSalvar.innerText = "Salvar Livro";
        });
    }
});

// --- FUNÇÕES GLOBAIS ---

window.editarLivro = function(livro) {
    const form = document.getElementById('form-cadastro');
    form.classList.remove('conteudo-oculto');
    // Garante que o form fique visível se estiver usando a lógica de ocultar
    if(form.style.display === 'none') form.style.display = 'block';

    document.getElementById('action').value = 'edit';
    document.getElementById('livro_id').value = livro.id_livro;
    document.getElementById('titulo').value = livro.titulo;
    document.getElementById('descricao').value = livro.descricao;
    document.getElementById('data_publi').value = livro.data_publi;
    document.getElementById('categoria').value = livro.categoria;
    
    // Preenche o Autor
    if(livro.id_autor) {
        document.getElementById('autor').value = livro.id_autor;
    }

    const existingPdf = document.getElementById('existingPdf');
    if (existingPdf) existingPdf.innerText = livro.pdf ? 'Arquivo atual: ' + livro.pdf : '';

    document.getElementById('btn-menu-criar').innerText = "Atualizar Livro";
    form.scrollIntoView({ behavior: 'smooth' });
};

// --- CATEGORIAS (ADICIONAR E EXCLUIR) ---

window.showNewCategoryForm = function() { document.getElementById('novaCategoriaModal').style.display = 'block'; };
window.hideNewCategoryForm = function() { document.getElementById('novaCategoriaModal').style.display = 'none'; };
window.showManageCategoryForm = function() { document.getElementById('gerenciarCategoriaModal').style.display = 'block'; };
window.hideManageCategoryForm = function() { document.getElementById('gerenciarCategoriaModal').style.display = 'none'; };

// --- AUTORES (ADICIONAR E EXCLUIR) - NOVO ---

window.showNewAutorForm = function() { document.getElementById('novoAutorModal').style.display = 'block'; };
window.hideNewAutorForm = function() { document.getElementById('novoAutorModal').style.display = 'none'; };
window.showManageAutorForm = function() { document.getElementById('gerenciarAutorModal').style.display = 'block'; };
window.hideManageAutorForm = function() { document.getElementById('gerenciarAutorModal').style.display = 'none'; };

// Salvar Categoria
window.salvarCategoria = function() {
    enviarDadosAjax('add_categoria', 'nome_categoria_modal', 'Categoria Criada!');
};

// Salvar Autor
window.salvarAutor = function() {
    enviarDadosAjax('add_autor', 'nome_autor_modal', 'Autor Criado!');
};

// Função Genérica para Salvar (Categoria ou Autor)
function enviarDadosAjax(action, inputId, successMsg) {
    const nomeInput = document.getElementById(inputId);
    const nome = nomeInput.value.trim();
    
    if (!nome) { alert('Insira um nome.'); return; }

    const formData = new FormData();
    formData.append('action', action);
    // O PHP espera 'nome_categoria' ou 'nome_autor', dependendo da action
    if (action === 'add_autor') formData.append('nome_autor', nome);
    else formData.append('nome_categoria', nome);

    // IMPORTANTE: Ajuste o caminho se necessário (mybooksAdmin.php)
    fetch('templates/biblioteca/admin/mybooksAdmin.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(successMsg);
            window.location.reload();
        } else {
            alert('Erro: ' + data.error);
        }
    })
    .catch(err => console.error("Erro Ajax:", err));
}

// Excluir Categoria
window.excluirCategoria = function(id) {
    excluirItemAjax('delete_categoria', 'id_categoria', id, 'Categoria excluída!');
};

// Excluir Autor
window.excluirAutor = function(id) {
    excluirItemAjax('delete_autor', 'id_autor', id, 'Autor excluído!');
};

// Função Genérica para Excluir
function excluirItemAjax(action, idKey, idVal, successMsg) {
    if (!confirm("Tem certeza que deseja excluir?")) return;

    const formData = new FormData();
    formData.append('action', action);
    formData.append(idKey, idVal);

    fetch('templates/biblioteca/admin/mybooksAdmin.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(successMsg);
            window.location.reload();
        } else {
            alert(data.error);
        }
    });
}

// Fechar Modais ao clicar fora
window.onclick = function(event) {
    const m1 = document.getElementById('novaCategoriaModal');
    const m2 = document.getElementById('gerenciarCategoriaModal');
    const m3 = document.getElementById('novoAutorModal');
    const m4 = document.getElementById('gerenciarAutorModal');
    
    if (event.target == m1) window.hideNewCategoryForm();
    if (event.target == m2) window.hideManageCategoryForm();
    if (event.target == m3) window.hideNewAutorForm();
    if (event.target == m4) window.hideManageAutorForm();
}