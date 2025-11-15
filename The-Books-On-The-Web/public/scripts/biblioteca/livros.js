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
            
            // Limpa avisos específicos de livro
            const avisosLivro = ["avisoTitulo", "avisoDescricao", "avisoDataPubli", "avisoCategoria", "avisoPdf"];
            avisosLivro.forEach(id => limparAviso(id));

            const actionInput = document.getElementById('action');
            const isEditMode = actionInput && actionInput.value === 'edit';

            // Validações (IDs de aviso conforme seu utilits.js)
            const titulo = validarTitulo('titulo', 'avisoTitulo');
            const descricao = validarDescricao('descricao', 'avisoDescricao');
            const dataPubli = validarDataPublicacao('data_publi', 'avisoDataPubli');
            const categoria = validarCategoria('categoria', 'avisoCategoria');
            const pdf = validarPdf('pdf_file', 'avisoPdf', !isEditMode); 

            if (titulo && descricao && dataPubli && categoria && pdf) {
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
            
            const avisosLivro = ["avisoTitulo", "avisoDescricao", "avisoDataPubli", "avisoCategoria", "avisoPdf"];
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

    document.getElementById('action').value = 'edit';
    document.getElementById('livro_id').value = livro.id_livro;
    document.getElementById('titulo').value = livro.titulo;
    document.getElementById('descricao').value = livro.descricao;
    document.getElementById('data_publi').value = livro.data_publi;
    document.getElementById('categoria').value = livro.categoria;

    const existingPdf = document.getElementById('existingPdf');
    if (existingPdf) existingPdf.innerText = livro.pdf ? 'Arquivo atual: ' + livro.pdf : '';

    document.getElementById('btn-menu-criar').innerText = "Atualizar Livro";
    form.scrollIntoView({ behavior: 'smooth' });
};

// --- CATEGORIAS (ADICIONAR E EXCLUIR) ---

window.showNewCategoryForm = function() {
    document.getElementById('novaCategoriaModal').style.display = 'block';
};

window.hideNewCategoryForm = function() {
    document.getElementById('novaCategoriaModal').style.display = 'none';
};

window.showManageCategoryForm = function() {
    document.getElementById('gerenciarCategoriaModal').style.display = 'block';
};

window.hideManageCategoryForm = function() {
    document.getElementById('gerenciarCategoriaModal').style.display = 'none';
};

// Salvar Categoria
window.salvarCategoria = function() {
    const nomeInput = document.getElementById('nome_categoria_modal');
    const nome = nomeInput.value.trim();
    
    if (!nome) { alert('Insira um nome.'); return; }

    const formData = new FormData();
    formData.append('action', 'add_categoria');
    formData.append('nome_categoria', nome);

    fetch('templates/biblioteca/mybooks.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Categoria Criada!');
            window.location.reload();
        } else {
            alert('Erro: ' + data.error);
        }
    });
};

// Excluir Categoria
window.excluirCategoria = function(id) {
    if (!confirm("Tem certeza que deseja excluir esta categoria?")) return;

    const formData = new FormData();
    formData.append('action', 'delete_categoria');
    formData.append('id_categoria', id);

    fetch('templates/biblioteca/mybooks.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Categoria excluída!');
            window.location.reload();
        } else {
            alert(data.error);
        }
    });
};

// Fechar Modais
window.onclick = function(event) {
    const m1 = document.getElementById('novaCategoriaModal');
    const m2 = document.getElementById('gerenciarCategoriaModal');
    if (event.target == m1) window.hideNewCategoryForm();
    if (event.target == m2) window.hideManageCategoryForm();
}