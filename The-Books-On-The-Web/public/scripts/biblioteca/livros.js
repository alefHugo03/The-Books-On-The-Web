// ARQUIVO: public/scripts/biblioteca/livros.js
import { 
    validarTitulo, 
    validarDescricao, 
    validarDataPublicacao, 
    validarCategoria, 
    validarPdf 
} from "../validations/validarLivros.js"; 

import { etapa, limparAviso } from "../validations/utilits.js";

// CAMINHO DA API
const API_URL = "/The-Books-On-The-Web/public/api/biblioteca/gerenciarLivros.php";

// --- VARIÁVEIS GLOBAIS ---
let todosLivros = []; 
let paginaAtual = 1;
let itensPorPagina = 10;

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Carregar dados iniciais
    carregarDadosTela();

    // 2. Listeners de Pesquisa e Paginação
    const inputBusca = document.getElementById('buscaLivroInput');
    const selectItens = document.getElementById('itensPorPagina');

    if(inputBusca) {
        inputBusca.addEventListener('input', function() { 
            paginaAtual = 1; 
            atualizarTabelaFrontend();
        });
    }

    if(selectItens) {
        selectItens.addEventListener('change', function() {
            itensPorPagina = parseInt(this.value);
            paginaAtual = 1;
            atualizarTabelaFrontend();
        });
    }

    // 3. Formulário de Livros
    const formLivro = document.getElementById('form-cadastro');
    if (formLivro) {
        formLivro.addEventListener('submit', function(event) {
            event.preventDefault(); 
            
            const avisosLivro = ["avisoTitulo", "avisoDescricao", "avisoDataPubli", "avisoCategoria", "avisoAutor", "avisoPdf"];
            avisosLivro.forEach(id => limparAviso(id));

            const actionInput = document.getElementById('action');
            const isEditMode = actionInput && actionInput.value === 'edit';

            const v1 = validarTitulo('titulo', 'avisoTitulo');
            const v2 = validarDescricao('descricao', 'avisoDescricao');
            const v3 = validarDataPublicacao('data_publi', 'avisoDataPubli');
            const v4 = validarCategoria('categoria', 'avisoCategoria');
            const v5 = validarCategoria('autor', 'avisoAutor'); 
            const v6 = validarPdf('pdf_file', 'avisoPdf', !isEditMode); 

            if (v1 && v2 && v3 && v4 && v5 && v6) {
                enviarFormularioLivro(new FormData(formLivro));
            }
        });
    }

    // 4. Auto-preencher título com PDF
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
    
    // 5. Resetar formulário
    const btnToggle = document.getElementById('btn-toggle-cadastro');
    if (btnToggle && formLivro) {
        btnToggle.addEventListener('click', function() {
            const form = document.getElementById('form-cadastro');
            
            if(form.classList.contains('conteudo-oculto') || form.style.display === 'none') {
                form.classList.remove('conteudo-oculto');
                form.style.display = 'block';
                
                document.getElementById('action').value = 'add';
                document.getElementById('livro_id').value = '';
                form.reset();
                document.getElementById('btn-menu-criar').innerText = "Salvar Livro";
                const existingPdf = document.getElementById('existingPdf');
                if(existingPdf) existingPdf.innerText = '';
            } else {
                form.classList.add('conteudo-oculto');
                setTimeout(() => { form.style.display = 'none'; }, 300);
            }
        });
    }
});

// --- COMUNICAÇÃO COM API ---

function carregarDadosTela() {
    fetch(API_URL + '?acao=listar_tudo')
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            todosLivros = data.livros;
            
            renderizarSelect('categoria', data.categorias, 'id_categoria', 'nome_categoria');
            renderizarSelect('autor', data.autores, 'id_autor', 'nome_autor');
            renderizarTabelaModal('lista-categorias-modal', data.categorias, 'nome_categoria', 'id_categoria', 'delete_categoria');
            renderizarTabelaModal('lista-autores-modal', data.autores, 'nome_autor', 'id_autor', 'delete_autor');

            atualizarTabelaFrontend();
        } else {
            console.error("Erro backend:", data.error);
        }
    })
    .catch(e => console.error("Erro fetch:", e));
}

function enviarFormularioLivro(formData) {
    fetch(API_URL, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert(data.msg || "Sucesso!");
            document.getElementById('form-cadastro').reset();
            document.getElementById('action').value = 'add';
            document.getElementById('livro_id').value = '';
            document.getElementById('existingPdf').innerText = '';
            carregarDadosTela(); 
        } else {
            alert("Erro: " + data.error);
        }
    })
    .catch(e => console.error(e));
}

// --- PAGINAÇÃO, FILTRO E CONTADOR ---

function atualizarTabelaFrontend() {
    const buscaInput = document.getElementById('buscaLivroInput');
    const termo = buscaInput ? buscaInput.value.toLowerCase() : '';
    
    // 1. Filtrar
    const livrosFiltrados = todosLivros.filter(livro => {
        const titulo = (livro.titulo || '').toLowerCase();
        const autor = (livro.nome_autor || '').toLowerCase();
        const categoria = (livro.nome_categoria || '').toLowerCase();
        return titulo.includes(termo) || autor.includes(termo) || categoria.includes(termo);
    });

    // 2. Atualizar Contador (NOVO)
    const contadorEl = document.getElementById('contador-livros');
    if(contadorEl) {
        contadorEl.innerText = `Total: ${livrosFiltrados.length}`;
    }

    // 3. Paginar
    const totalItens = livrosFiltrados.length;
    const totalPaginas = Math.ceil(totalItens / itensPorPagina);
    
    if (paginaAtual > totalPaginas) paginaAtual = totalPaginas > 0 ? totalPaginas : 1;
    if (paginaAtual < 1) paginaAtual = 1;

    const inicio = (paginaAtual - 1) * itensPorPagina;
    const fim = inicio + itensPorPagina;
    const livrosPagina = livrosFiltrados.slice(inicio, fim);

    // 4. Renderizar
    renderizarTabela(livrosPagina);
    renderizarBotoesPaginacao(totalPaginas);
}

function renderizarTabela(livros) {
    const tbody = document.getElementById('tabela-livros-corpo');
    if(!tbody) return;
    tbody.innerHTML = '';

    if(livros.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" align="center" style="padding:20px;">Nenhum livro encontrado.</td></tr>';
        return;
    }

    livros.forEach(livro => {
        const tr = document.createElement('tr');
        const jsonLivro = JSON.stringify(livro).replace(/"/g, '&quot;');
        
        tr.innerHTML = `
            <td><strong>${livro.titulo}</strong></td>
            <td>${livro.nome_autor || '<em style="color:#999">N/A</em>'}</td>
            <td><span class="categoria-badge">${livro.nome_categoria}</span></td>
            <td style="text-align:center;">
                <button class="btn-small btn-editar" onclick="preencherFormulario(${jsonLivro})">Editar</button>
                <button class="btn-small btn-excluir" onclick="deletarItem('delete', ${livro.id_livro})">Excluir</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderizarBotoesPaginacao(totalPaginas) {
    const container = document.getElementById('paginacaoContainer');
    if(!container) return;
    container.innerHTML = '';

    if(totalPaginas <= 1) return;

    // Botão Anterior
    const btnPrev = document.createElement('button');
    btnPrev.innerText = '<';
    btnPrev.disabled = paginaAtual === 1;
    btnPrev.onclick = () => { paginaAtual--; atualizarTabelaFrontend(); };
    container.appendChild(btnPrev);

    // Botões Numéricos
    let startPage = Math.max(1, paginaAtual - 2);
    let endPage = Math.min(totalPaginas, paginaAtual + 2);

    if (startPage > 1) {
        const btnFirst = document.createElement('button');
        btnFirst.innerText = '1';
        btnFirst.onclick = () => { paginaAtual = 1; atualizarTabelaFrontend(); };
        container.appendChild(btnFirst);
        if (startPage > 2) container.appendChild(document.createTextNode(' ... '));
    }

    for (let i = startPage; i <= endPage; i++) {
        const btn = document.createElement('button');
        btn.innerText = i;
        if (i === paginaAtual) btn.classList.add('ativo');
        btn.onclick = () => { paginaAtual = i; atualizarTabelaFrontend(); };
        container.appendChild(btn);
    }

    if (endPage < totalPaginas) {
        if (endPage < totalPaginas - 1) container.appendChild(document.createTextNode(' ... '));
        const btnLast = document.createElement('button');
        btnLast.innerText = totalPaginas;
        btnLast.onclick = () => { paginaAtual = totalPaginas; atualizarTabelaFrontend(); };
        container.appendChild(btnLast);
    }

    // Botão Próximo
    const btnNext = document.createElement('button');
    btnNext.innerText = '>';
    btnNext.disabled = paginaAtual === totalPaginas;
    btnNext.onclick = () => { paginaAtual++; atualizarTabelaFrontend(); };
    container.appendChild(btnNext);
}

// --- OUTRAS FUNÇÕES (MANTIDAS) ---

function renderizarSelect(idSelect, dados, keyId, keyNome) {
    const select = document.getElementById(idSelect);
    if(!select) return;
    const valorAtual = select.value; 
    
    select.innerHTML = '<option value="" disabled selected>Selecione...</option>';
    dados.forEach(item => {
        select.innerHTML += `<option value="${item[keyId]}">${item[keyNome]}</option>`;
    });

    if(valorAtual) select.value = valorAtual;
}

function renderizarTabelaModal(tbodyId, dados, keyNome, keyId, actionDelete) {
    const tbody = document.getElementById(tbodyId);
    if(!tbody) return;
    tbody.innerHTML = '';

    if(dados.length === 0) {
        tbody.innerHTML = '<tr><td>Vazio.</td></tr>';
        return;
    }

    dados.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding:8px; border-bottom:1px solid #eee;">${item[keyNome]}</td>
            <td align="right" style="padding:8px; border-bottom:1px solid #eee;">
                <button class="btn-small btn-excluir" onclick="deletarItem('${actionDelete}', ${item[keyId]})">X</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Funções Globais (window)
window.preencherFormulario = function(livro) {
    const form = document.getElementById('form-cadastro');
    form.classList.remove('conteudo-oculto');
    form.style.display = 'block';

    document.getElementById('action').value = 'edit';
    document.getElementById('livro_id').value = livro.id_livro;
    document.getElementById('titulo').value = livro.titulo;
    document.getElementById('descricao').value = livro.descricao;
    document.getElementById('data_publi').value = livro.data_publi;
    
    setTimeout(() => {
        const catSelect = document.getElementById('categoria');
        const autSelect = document.getElementById('autor');
        if(catSelect) catSelect.value = livro.categoria;
        if(autSelect && livro.id_autor) autSelect.value = livro.id_autor;
    }, 100);

    document.getElementById('btn-menu-criar').innerText = "Atualizar Livro";
    const existingPdf = document.getElementById('existingPdf');
    if (existingPdf) existingPdf.innerText = livro.pdf ? 'PDF Atual: ' + livro.pdf : '';

    form.scrollIntoView({behavior: "smooth"});
};

window.deletarItem = function(action, id) {
    if(!confirm("Tem certeza que deseja excluir?")) return;
    
    const fd = new FormData();
    fd.append('action', action);
    if(action === 'delete') fd.append('livro_id', id);
    else if(action.includes('categoria')) fd.append('id_categoria', id);
    else if(action.includes('autor')) fd.append('id_autor', id);
    
    fetch(API_URL, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            alert(data.msg || "Concluído!");
            carregarDadosTela(); 
        } else {
            alert("Erro: " + data.error);
        }
    })
    .catch(e => console.error(e));
};

// Modais
window.salvarCategoria = function() { salvarItemModal('add_categoria', 'nome_categoria_modal', 'nome_categoria'); };
window.salvarAutor = function() { salvarItemModal('add_autor', 'nome_autor_modal', 'nome_autor'); };

function salvarItemModal(action, inputId, keyPost) {
    const input = document.getElementById(inputId);
    const valor = input.value.trim();
    if(!valor) { alert("Digite um nome."); return; }

    const fd = new FormData();
    fd.append('action', action);
    fd.append(keyPost, valor);

    fetch(API_URL, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            input.value = ''; 
            alert("Cadastrado com sucesso!");
            carregarDadosTela(); 
        } else {
            alert("Erro: " + data.error);
        }
    })
    .catch(e => console.error(e));
}

window.showNewCategoryForm = () => document.getElementById('novaCategoriaModal').style.display = 'block';
window.showNewAutorForm = () => document.getElementById('novoAutorModal').style.display = 'block';
window.showManageCategoryForm = () => document.getElementById('gerenciarCategoriaModal').style.display = 'block';
window.showManageAutorForm = () => document.getElementById('gerenciarAutorModal').style.display = 'block';
window.hideNewCategoryForm = () => document.getElementById('novaCategoriaModal').style.display = 'none';
window.hideNewAutorForm = () => document.getElementById('novoAutorModal').style.display = 'none';
window.hideManageCategoryForm = () => document.getElementById('gerenciarCategoriaModal').style.display = 'none';
window.hideManageAutorForm = () => document.getElementById('gerenciarAutorModal').style.display = 'none';

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}