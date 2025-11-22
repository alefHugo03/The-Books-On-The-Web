import FormValidator from "./FormValidator.js";
import { limparAviso, avisoFalas } from "../validations/utilits.js";

export default class GerenciadorLivros {
    constructor(config) {
        this.apiUrl = config.apiUrl;
        this.form = document.getElementById(config.formId);
        this.tabelaCorpo = document.getElementById(config.tabelaCorpoId);
        this.paginacaoContainer = document.getElementById(config.paginacaoId);
        this.inputBusca = document.getElementById(config.buscaInputId);
        this.selectItens = document.getElementById(config.itensPorPaginaId);
        this.btnToggle = document.getElementById(config.btnToggleId);
        
        this.todosLivros = [];
        this.paginaAtual = 1;
        this.itensPorPagina = 10;
        this.tomSelects = { autor: null, categoria: null }; // TomSelect instances
        
        this.validator = new FormValidator();

        this.filtrarEAtualizar = this.filtrarEAtualizar.bind(this);
        this.processarFormulario = this.processarFormulario.bind(this);
        this.gerenciarCliquesTabela = this.gerenciarCliquesTabela.bind(this);
        this.toggleFormulario = this.toggleFormulario.bind(this);
    }

    init() {
        console.log("Livros Manager: Iniciado");
        this.carregarDados();
        
        if (this.inputBusca) this.inputBusca.addEventListener('input', () => { this.paginaAtual = 1; this.filtrarEAtualizar(); });
        if (this.selectItens) this.selectItens.addEventListener('change', (e) => { this.itensPorPagina = parseInt(e.target.value); this.paginaAtual = 1; this.filtrarEAtualizar(); });
        if (this.form) this.form.addEventListener('submit', this.processarFormulario);
        if (this.tabelaCorpo) this.tabelaCorpo.addEventListener('click', this.gerenciarCliquesTabela);
        if (this.btnToggle) this.btnToggle.addEventListener('click', this.toggleFormulario);
        
        this._setupPdfInput();
    }

    carregarDados() {
        fetch(this.apiUrl + '?acao=listar_tudo')
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                this.todosLivros = data.livros;
                this._initTomSelect('autor', data.autores, 'id_autor', 'nome_autor');
                this._initTomSelect('categoria', data.categorias, 'id_categoria', 'nome_categoria');
                this.filtrarEAtualizar();
            }
        });
    }

    processarFormulario(e) {
        e.preventDefault();
        const isEdit = document.getElementById('action').value === 'edit';
        
        const v1 = this.validator.validarCampo('titulo', 'avisoTitulo', ['obrigatorio']);
        const v2 = this.validator.validarCampo('data_publi', 'avisoDataPubli', ['obrigatorio']);
        
        // Validação manual de selects e PDF
        let v3 = true, v4 = true, v5 = true;
        if(!document.getElementById('categoria').value) { avisoFalas("Selecione categoria.", "avisoCategoria"); v3 = false; }
        if(!document.getElementById('autor').value) { avisoFalas("Selecione autor.", "avisoAutor"); v4 = false; }
        
        const pdf = document.getElementById('pdf_file');
        if(!isEdit && (!pdf.files || pdf.files.length === 0)) { avisoFalas("PDF obrigatório.", "avisoPdf"); v5 = false; }

        if (v1 && v2 && v3 && v4 && v5) {
            this._enviarDados(new FormData(this.form));
        }
    }

    _enviarDados(formData) {
        fetch(this.apiUrl, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                alert("Salvo com sucesso!");
                this._resetarForm();
                this.carregarDados();
            } else { alert(data.error); }
        });
    }

    // ... Métodos de Tabela, Paginação e TomSelect (resumidos para caber, mas mantendo a lógica) ...
    filtrarEAtualizar() {
        const termo = this.inputBusca ? this.inputBusca.value.toLowerCase() : '';
        const filtrados = this.todosLivros.filter(l => (l.titulo||'').toLowerCase().includes(termo));
        
        // Paginação Lógica
        const total = filtrados.length;
        const pags = Math.ceil(total / this.itensPorPagina);
        if (this.paginaAtual > pags) this.paginaAtual = pags || 1;
        
        const inicio = (this.paginaAtual - 1) * this.itensPorPagina;
        this._renderizarTabela(filtrados.slice(inicio, inicio + this.itensPorPagina));
        this._renderizarPaginacao(pags);
    }

    _renderizarTabela(lista) {
        this.tabelaCorpo.innerHTML = '';
        if(lista.length === 0) { this.tabelaCorpo.innerHTML = '<tr><td colspan="4" align="center">Nada encontrado.</td></tr>'; return; }
        
        lista.forEach(l => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${l.titulo}</strong></td>
                <td>${l.nomes_autores || 'N/A'}</td>
                <td><span class="categoria-badge">${l.nomes_categorias || 'N/A'}</span></td>
                <td align="center">
                    <button class="btn-small btn-editar acao-tabela" data-id="${l.id_livro}" data-act="editar">Editar</button>
                    <button class="btn-small btn-excluir acao-tabela" data-id="${l.id_livro}" data-act="excluir">Excluir</button>
                </td>
            `;
            this.tabelaCorpo.appendChild(tr);
        });
    }

    gerenciarCliquesTabela(e) {
        const btn = e.target;
        if (!btn.classList.contains('acao-tabela')) return;
        const id = btn.dataset.id;
        if (btn.dataset.act === 'excluir') this._excluir(id);
        if (btn.dataset.act === 'editar') this._preencherEdicao(this.todosLivros.find(l => l.id_livro == id));
    }

    _excluir(id) {
        if(!confirm("Excluir?")) return;
        const fd = new FormData(); fd.append('action', 'delete'); fd.append('livro_id', id);
        fetch(this.apiUrl, { method: 'POST', body: fd }).then(r => r.json()).then(() => this.carregarDados());
    }

    _preencherEdicao(l) {
        this.form.classList.remove('conteudo-oculto');
        this.form.style.display = 'grid';
        document.getElementById('action').value = 'edit';
        document.getElementById('livro_id').value = l.id_livro;
        document.getElementById('titulo').value = l.titulo;
        document.getElementById('data_publi').value = l.data_publi;
        document.getElementById('descricao').value = l.descricao;
        
        if(this.tomSelects.autor && l.ids_autores) { this.tomSelects.autor.clear(); this.tomSelects.autor.setValue(l.ids_autores.toString().split(',')); }
        if(this.tomSelects.categoria && l.ids_categorias) { this.tomSelects.categoria.clear(); this.tomSelects.categoria.setValue(l.ids_categorias.toString().split(',')); }
        
        document.getElementById('btn-menu-criar').innerText = "Atualizar";
        this.form.scrollIntoView({behavior:'smooth'});
    }

    _resetarForm() {
        this.form.reset();
        document.getElementById('action').value = 'add';
        document.getElementById('livro_id').value = '';
        if(this.tomSelects.autor) this.tomSelects.autor.clear();
        if(this.tomSelects.categoria) this.tomSelects.categoria.clear();
        document.getElementById('btn-menu-criar').innerText = "Salvar Livro";
    }

    toggleFormulario() {
        this.form.classList.toggle('conteudo-oculto');
        if(!this.form.classList.contains('conteudo-oculto')) {
            this.form.style.display = 'grid';
            this._resetarForm();
        } else {
            setTimeout(() => this.form.style.display = 'none', 300);
        }
    }
    
    _initTomSelect(id, dados, kId, kNome) {
        if(this.tomSelects[id]) { this.tomSelects[id].destroy(); }
        const sel = document.getElementById(id);
        sel.innerHTML = '';
        dados.forEach(d => { const o = document.createElement('option'); o.value = d[kId]; o.text = d[kNome]; sel.appendChild(o); });
        this.tomSelects[id] = new TomSelect(`#${id}`, { plugins: ['remove_button'], create: false });
    }

    _setupPdfInput() {
        const pdf = document.getElementById('pdf_file');
        if(pdf) pdf.addEventListener('change', function() {
            if(this.files[0] && document.getElementById('titulo').value === '') {
                document.getElementById('titulo').value = this.files[0].name.replace('.pdf','');
            }
        });
    }
}