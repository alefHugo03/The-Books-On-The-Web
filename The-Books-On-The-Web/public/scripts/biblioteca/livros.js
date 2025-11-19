// ARQUIVO: public/scripts/biblioteca/livros.js
import { 
    validarTitulo, 
    validarDescricao, 
    validarDataPublicacao, 
    validarCategoria, 
    validarPdf 
} from "../validations/validarLivros.js"; 

import { etapa, limparAviso } from "../validations/utilits.js";

const API_URL = "/The-Books-On-The-Web/public/api/biblioteca/gerenciarLivros.php";

let todosLivros = []; 
let paginaAtual = 1;
let itensPorPagina = 10;

// Variáveis Tom Select
let tomAutor = null;
let tomCategoria = null;
let tomEditora = null;

document.addEventListener("DOMContentLoaded", function() {
    carregarDadosTela();

    const inputBusca = document.getElementById('buscaLivroInput');
    const selectItens = document.getElementById('itensPorPagina');

    if(inputBusca) {
        inputBusca.addEventListener('input', () => { paginaAtual = 1; atualizarTabelaFrontend(); });
    }
    if(selectItens) {
        selectItens.addEventListener('change', function() {
            itensPorPagina = parseInt(this.value);
            paginaAtual = 1;
            atualizarTabelaFrontend();
        });
    }

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
            
            // Validação Tom Select
            const catVal = document.getElementById('categoria').value;
            const autVal = document.getElementById('autor').value;
            
            let v4 = true; let v5 = true;
            if(!catVal) { document.getElementById('avisoCategoria').innerText = "Selecione ao menos uma categoria."; v4 = false; }
            if(!autVal) { document.getElementById('avisoAutor').innerText = "Selecione ao menos um autor."; v5 = false; }

            const v6 = validarPdf('pdf_file', 'avisoPdf', !isEditMode); 

            if (v1 && v2 && v3 && v4 && v5 && v6) {
                enviarFormularioLivro(new FormData(formLivro));
            }
        });
    }

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
    
    // --- CORREÇÃO DO TOGGLE (GRID) ---
    const btnToggle = document.getElementById('btn-toggle-cadastro');
    if (btnToggle && formLivro) {
        btnToggle.addEventListener('click', function() {
            const form = document.getElementById('form-cadastro');
            
            // Se está oculto (tem a classe ou display none)
            if(form.classList.contains('conteudo-oculto') || form.style.display === 'none') {
                form.classList.remove('conteudo-oculto');
                form.style.display = 'grid'; // <--- IMPORTANTE: Usa GRID, não block
                
                // Reseta para modo Adicionar
                document.getElementById('action').value = 'add';
                document.getElementById('livro_id').value = '';
                form.reset();
                
                if(tomAutor) tomAutor.clear();
                if(tomCategoria) tomCategoria.clear();
                if(tomEditora) tomEditora.clear();

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

// --- API ---

function carregarDadosTela() {
    fetch(API_URL + '?acao=listar_tudo')
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            todosLivros = data.livros;
            
            inicializarTomSelect('autor', data.autores, 'id_autor', 'nome_autor', true);
            inicializarTomSelect('categoria', data.categorias, 'id_categoria', 'nome_categoria', true);
            inicializarTomSelect('editora', data.editoras, 'id_editora', 'nome_editora', false);
            
            renderizarTabelaModal('lista-categorias-modal', data.categorias, 'nome_categoria', 'id_categoria', 'delete_categoria');
            renderizarTabelaModal('lista-autores-modal', data.autores, 'nome_autor', 'id_autor', 'delete_autor');
            renderizarTabelaModal('lista-editoras-modal', data.editoras, 'nome_editora', 'id_editora', 'delete_editora');

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
            const form = document.getElementById('form-cadastro');
            form.reset();
            if(tomAutor) tomAutor.clear();
            if(tomCategoria) tomCategoria.clear();
            if(tomEditora) tomEditora.clear();
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

// --- FRONTEND ---

function atualizarTabelaFrontend() {
    const buscaInput = document.getElementById('buscaLivroInput');
    const termo = buscaInput ? buscaInput.value.toLowerCase() : '';
    
    const livrosFiltrados = todosLivros.filter(livro => {
        const titulo = (livro.titulo || '').toLowerCase();
        const autor = (livro.nomes_autores || '').toLowerCase();
        const categoria = (livro.nomes_categorias || '').toLowerCase();
        const editora = (livro.nome_editora || '').toLowerCase(); 
        return titulo.includes(termo) || autor.includes(termo) || categoria.includes(termo) || editora.includes(termo);
    });

    const contadorEl = document.getElementById('contador-livros');
    if(contadorEl) contadorEl.innerText = `Total: ${livrosFiltrados.length}`;

    const totalItens = livrosFiltrados.length;
    const totalPaginas = Math.ceil(totalItens / itensPorPagina);
    
    if (paginaAtual > totalPaginas) paginaAtual = totalPaginas > 0 ? totalPaginas : 1;
    if (paginaAtual < 1) paginaAtual = 1;

    const inicio = (paginaAtual - 1) * itensPorPagina;
    const fim = inicio + itensPorPagina;
    const livrosPagina = livrosFiltrados.slice(inicio, fim);

    renderizarTabela(livrosPagina);
    renderizarBotoesPaginacao(totalPaginas);
}

function renderizarTabela(livros) {
    const tbody = document.getElementById('tabela-livros-corpo');
    if(!tbody) return;
    tbody.innerHTML = '';

    if(livros.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" align="center" style="padding:20px;">Nenhum livro encontrado.</td></tr>';
        return;
    }

    livros.forEach(livro => {
        const tr = document.createElement('tr');
        const jsonLivro = JSON.stringify(livro).replace(/"/g, '&quot;');
        
        tr.innerHTML = `
            <td><strong>${livro.titulo}</strong></td>
            <td>${livro.nomes_autores || '<em style="color:#999">N/A</em>'}</td>
            <td>${livro.nome_editora || '<em style="color:#999">-</em>'}</td>
            <td><span class="categoria-badge">${livro.nomes_categorias || 'Sem categoria'}</span></td>
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

    const btnPrev = document.createElement('button');
    btnPrev.innerText = '<';
    btnPrev.disabled = paginaAtual === 1;
    btnPrev.onclick = () => { paginaAtual--; atualizarTabelaFrontend(); };
    container.appendChild(btnPrev);

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

    const btnNext = document.createElement('button');
    btnNext.innerText = '>';
    btnNext.disabled = paginaAtual === totalPaginas;
    btnNext.onclick = () => { paginaAtual++; atualizarTabelaFrontend(); };
    container.appendChild(btnNext);
}

// --- AUXILIARES ---

function inicializarTomSelect(idSelect, dados, keyId, keyNome, isMultiple) {
    const select = document.getElementById(idSelect);
    if (!select) return;

    if (idSelect === 'autor' && tomAutor) { tomAutor.destroy(); tomAutor = null; }
    if (idSelect === 'categoria' && tomCategoria) { tomCategoria.destroy(); tomCategoria = null; }
    if (idSelect === 'editora' && tomEditora) { tomEditora.destroy(); tomEditora = null; }

    select.innerHTML = '';
    if (!isMultiple) select.innerHTML = '<option value="">Selecione...</option>';
    
    dados.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item[keyId];
        opt.text = item[keyNome];
        select.appendChild(opt);
    });

    const config = {
        plugins: isMultiple ? ['remove_button'] : [], 
        create: false, 
        sortField: { field: "text", direction: "asc" },
        placeholder: isMultiple ? "Selecione..." : "Selecione uma opção..."
    };

    const instance = new TomSelect(`#${idSelect}`, config);
    if (idSelect === 'autor') tomAutor = instance;
    if (idSelect === 'categoria') tomCategoria = instance;
    if (idSelect === 'editora') tomEditora = instance;
}

function renderizarTabelaModal(tbodyId, dados, keyNome, keyId, actionDelete) {
    const tbody = document.getElementById(tbodyId);
    if(!tbody) return;
    tbody.innerHTML = '';
    if(dados.length === 0) { tbody.innerHTML = '<tr><td>Vazio.</td></tr>'; return; }
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

// --- GLOBAIS ---

window.preencherFormulario = function(livro) {
    const form = document.getElementById('form-cadastro');
    // Força mostrar como GRID
    form.classList.remove('conteudo-oculto');
    form.style.display = 'grid';

    document.getElementById('action').value = 'edit';
    document.getElementById('livro_id').value = livro.id_livro;
    document.getElementById('titulo').value = livro.titulo;
    document.getElementById('descricao').value = livro.descricao;
    document.getElementById('data_publi').value = livro.data_publi;
    
    if(tomEditora) {
        tomEditora.clear(); 
        if(livro.fk_editora) tomEditora.setValue(livro.fk_editora);
    }

    if(tomCategoria && livro.ids_categorias) {
        tomCategoria.clear();
        tomCategoria.setValue(livro.ids_categorias.toString().split(','));
    }

    if(tomAutor && livro.ids_autores) {
        tomAutor.clear();
        tomAutor.setValue(livro.ids_autores.toString().split(','));
    }

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
    else if(action.includes('editora')) fd.append('id_editora', id);
    
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

window.salvarCategoria = function() { salvarItemModal('add_categoria', 'nome_categoria_modal', 'nome_categoria'); };
window.salvarAutor = function() { salvarItemModal('add_autor', 'nome_autor_modal', 'nome_autor'); };
window.salvarEditora = function() { salvarItemModal('add_editora', 'nome_editora_modal', 'nome_editora'); };

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
window.showManageCategoryForm = () => document.getElementById('gerenciarCategoriaModal').style.display = 'block';
window.hideNewCategoryForm = () => document.getElementById('novaCategoriaModal').style.display = 'none';
window.hideManageCategoryForm = () => document.getElementById('gerenciarCategoriaModal').style.display = 'none';
window.showNewAutorForm = () => document.getElementById('novoAutorModal').style.display = 'block';
window.showManageAutorForm = () => document.getElementById('gerenciarAutorModal').style.display = 'block';
window.hideNewAutorForm = () => document.getElementById('novoAutorModal').style.display = 'none';
window.hideManageAutorForm = () => document.getElementById('gerenciarAutorModal').style.display = 'none';
window.showNewEditoraForm = () => document.getElementById('novaEditoraModal').style.display = 'block';
window.showManageEditoraForm = () => document.getElementById('gerenciarEditoraModal').style.display = 'block';
window.hideNewEditoraForm = () => document.getElementById('novaEditoraModal').style.display = 'none';
window.hideManageEditoraForm = () => document.getElementById('gerenciarEditoraModal').style.display = 'none';

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}