import FormValidator from "./FormValidator.js";
import { limparAviso, avisoFalas } from "../validations/utilits.js";

export default class GerenciadorLivros {
    constructor(config) {
        // Configurações
        this.apiUrl = config.apiUrl;
        
        // Elementos principais
        this.form = document.getElementById(config.formId);
        this.tabelaCorpo = document.getElementById(config.tabelaCorpoId);
        this.paginacaoContainer = document.getElementById(config.paginacaoId);
        this.inputBusca = document.getElementById(config.buscaInputId);
        this.selectItens = document.getElementById(config.itensPorPaginaId);
        this.btnToggle = document.getElementById(config.btnToggleId);
        
        // Estado da Aplicação
        this.todosLivros = [];
        this.paginaAtual = 1;
        this.itensPorPagina = 10;
        this.tomSelects = { autor: null, categoria: null, editora: null }; // Armazena as instâncias
        
        // Instância do Validador
        this.validator = new FormValidator();

        // Bindings (para garantir o 'this' correto nos eventos)
        this.filtrarEAtualizar = this.filtrarEAtualizar.bind(this);
        this.processarFormulario = this.processarFormulario.bind(this);
        this.gerenciarCliquesTabela = this.gerenciarCliquesTabela.bind(this);
        this.toggleFormulario = this.toggleFormulario.bind(this);
    }

    init() {
        console.log("Gerenciador Livros: Iniciado (POO)");
        this.carregarDados();

        // Listeners
        if (this.inputBusca) this.inputBusca.addEventListener('input', () => { this.paginaAtual = 1; this.filtrarEAtualizar(); });
        
        if (this.selectItens) this.selectItens.addEventListener('change', (e) => {
            this.itensPorPagina = parseInt(e.target.value);
            this.paginaAtual = 1;
            this.filtrarEAtualizar();
        });

        if (this.form) this.form.addEventListener('submit', this.processarFormulario);
        
        if (this.tabelaCorpo) this.tabelaCorpo.addEventListener('click', this.gerenciarCliquesTabela);
        
        if (this.btnToggle) this.btnToggle.addEventListener('click', this.toggleFormulario);

        this._setupPdfInput();
    }

    /* --- LÓGICA DE DADOS (API) --- */

    carregarDados() {
        fetch(this.apiUrl + '?acao=listar_tudo')
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                this.todosLivros = data.livros;
                
                // Inicializa Tom Selects com os dados recebidos
                this._inicializarTomSelect('autor', data.autores, 'id_autor', 'nome_autor', true);
                this._inicializarTomSelect('categoria', data.categorias, 'id_categoria', 'nome_categoria', true);
                this._inicializarTomSelect('editora', data.editoras, 'id_editora', 'nome_editora', false);
                
                // Atualiza a tela
                this.filtrarEAtualizar();
            } else {
                console.error("Erro backend:", data.error);
            }
        })
        .catch(e => console.error("Erro fetch:", e));
    }

    enviarDados(formData) {
        fetch(this.apiUrl, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                alert(data.msg || "Sucesso!");
                this._resetarFormulario();
                this.carregarDados(); 
            } else {
                alert("Erro: " + data.error);
            }
        })
        .catch(e => console.error(e));
    }

    excluirItem(action, id) {
        if(!confirm("Tem certeza que deseja excluir?")) return;
        
        const fd = new FormData();
        fd.append('action', action);
        
        // Lógica para saber qual ID enviar baseada na action
        if(action === 'delete') fd.append('livro_id', id);
        // Adicione aqui lógica para deletar autor/categoria se necessário na tabela principal
        
        fetch(this.apiUrl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                alert("Excluído com sucesso!");
                this.carregarDados();
            } else {
                alert("Erro ao excluir: " + data.error);
            }
        })
        .catch(e => console.error(e));
    }

    /* --- LÓGICA DE FRONTEND (FILTROS E PAGINAÇÃO) --- */

    filtrarEAtualizar() {
        const termo = this.inputBusca ? this.inputBusca.value.toLowerCase() : '';
        
        // Filtra na memória (Client-side filtering)
        const livrosFiltrados = this.todosLivros.filter(livro => {
            const titulo = (livro.titulo || '').toLowerCase();
            const autor = (livro.nomes_autores || '').toLowerCase();
            const categoria = (livro.nomes_categorias || '').toLowerCase();
            const editora = (livro.nome_editora || '').toLowerCase(); 
            return titulo.includes(termo) || autor.includes(termo) || categoria.includes(termo) || editora.includes(termo);
        });

        // Lógica de Paginação
        const totalItens = livrosFiltrados.length;
        const totalPaginas = Math.ceil(totalItens / this.itensPorPagina);
        
        if (this.paginaAtual > totalPaginas) this.paginaAtual = totalPaginas > 0 ? totalPaginas : 1;
        if (this.paginaAtual < 1) this.paginaAtual = 1;

        const inicio = (this.paginaAtual - 1) * this.itensPorPagina;
        const fim = inicio + this.itensPorPagina;
        const livrosPagina = livrosFiltrados.slice(inicio, fim);

        // Atualiza contadores
        const contadorEl = document.getElementById('contador-livros');
        if(contadorEl) contadorEl.innerText = `Total: ${totalItens}`;

        this._renderizarTabela(livrosPagina);
        this._renderizarPaginacao(totalPaginas);
    }

    _renderizarTabela(livros) {
        this.tabelaCorpo.innerHTML = '';
        if(livros.length === 0) {
            this.tabelaCorpo.innerHTML = '<tr><td colspan="5" align="center">Nenhum livro encontrado.</td></tr>';
            return;
        }

        livros.forEach(livro => {
            const tr = document.createElement('tr');
            
            // AQUI ESTÁ A MÁGICA: Em vez de passar o objeto JSON no onclick, passamos apenas o ID no data-id
            tr.innerHTML = `
                <td><strong>${livro.titulo}</strong></td>
                <td>${livro.nomes_autores || '<em style="color:#999">N/A</em>'}</td>
                <td>${livro.nome_editora || '<em style="color:#999">-</em>'}</td>
                <td><span class="categoria-badge">${livro.nomes_categorias || 'Sem categoria'}</span></td>
                <td style="text-align:center;">
                    <button class="btn-small btn-editar acao-tabela" data-id="${livro.id_livro}" data-acao="editar">Editar</button>
                    <button class="btn-small btn-excluir acao-tabela" data-id="${livro.id_livro}" data-acao="excluir">Excluir</button>
                </td>
            `;
            this.tabelaCorpo.appendChild(tr);
        });
    }

    /* --- EVENT DELEGATION (SUBSTITUI O ONCLICK) --- */
    
    gerenciarCliquesTabela(event) {
        const btn = event.target;
        if(!btn.classList.contains('acao-tabela')) return;

        const id = btn.dataset.id;
        const acao = btn.dataset.acao;

        if (acao === 'editar') {
            // Busca o objeto completo na memória usando o ID
            const livro = this.todosLivros.find(l => l.id_livro == id);
            if(livro) this.preencherFormularioEdicao(livro);
        } else if (acao === 'excluir') {
            this.excluirItem('delete', id);
        }
    }

    /* --- FORMULÁRIOS E VALIDAÇÃO --- */

    processarFormulario(event) {
        event.preventDefault();
        
        // Limpa avisos antigos
        const campos = ["avisoTitulo", "avisoDescricao", "avisoDataPubli", "avisoCategoria", "avisoAutor", "avisoPdf"];
        campos.forEach(id => limparAviso(id));

        const isEditMode = document.getElementById('action').value === 'edit';

        // 1. Valida campos texto com a Classe FormValidator
        const vTitulo = this.validator.validarCampo('titulo', 'avisoTitulo', ['obrigatorio']);
        const vDesc = this.validator.validarCampo('descricao', 'avisoDescricao', []); // Opcional
        const vData = this.validator.validarCampo('data_publi', 'avisoDataPubli', ['obrigatorio']);

        // 2. Validação Manual de Selects (Tom Select esconde o select original, o valor fica no hidden ou no original)
        let vCat = true, vAut = true;
        if(!document.getElementById('categoria').value) { 
            avisoFalas("Selecione uma categoria.", "avisoCategoria"); vCat = false; 
        }
        if(!document.getElementById('autor').value) { 
            avisoFalas("Selecione um autor.", "avisoAutor"); vAut = false; 
        }

        // 3. Validação de PDF
        let vPdf = true;
        const pdfInput = document.getElementById('pdf_file');
        // Obrigatório apenas se não for edição (ou seja, cadastro novo)
        if (!isEditMode && (!pdfInput.files || pdfInput.files.length === 0)) {
            avisoFalas("PDF obrigatório.", "avisoPdf"); vPdf = false;
        }
        if (pdfInput.files.length > 0 && pdfInput.files[0].type !== "application/pdf") {
            avisoFalas("Apenas PDF.", "avisoPdf"); vPdf = false;
        }

        if(vTitulo && vDesc && vData && vCat && vAut && vPdf) {
            this.enviarDados(new FormData(this.form));
        }
    }

    preencherFormularioEdicao(livro) {
        // Abre o form se estiver fechado
        this.form.classList.remove('conteudo-oculto');
        this.form.style.display = 'grid';

        document.getElementById('action').value = 'edit';
        document.getElementById('livro_id').value = livro.id_livro;
        document.getElementById('titulo').value = livro.titulo;
        document.getElementById('descricao').value = livro.descricao;
        document.getElementById('data_publi').value = livro.data_publi;
        
        // Define valores no Tom Select
        if(this.tomSelects.editora) {
            this.tomSelects.editora.clear();
            if(livro.fk_editora) this.tomSelects.editora.setValue(livro.fk_editora);
        }
        if(this.tomSelects.categoria && livro.ids_categorias) {
            this.tomSelects.categoria.clear();
            this.tomSelects.categoria.setValue(livro.ids_categorias.toString().split(','));
        }
        if(this.tomSelects.autor && livro.ids_autores) {
            this.tomSelects.autor.clear();
            this.tomSelects.autor.setValue(livro.ids_autores.toString().split(','));
        }

        document.getElementById('btn-menu-criar').innerText = "Atualizar Livro";
        const existingPdf = document.getElementById('existingPdf');
        if (existingPdf) existingPdf.innerText = livro.pdf ? 'PDF Atual: ' + livro.pdf : '';

        this.form.scrollIntoView({behavior: "smooth"});
    }

    toggleFormulario() {
        if(this.form.classList.contains('conteudo-oculto') || this.form.style.display === 'none') {
            this.form.classList.remove('conteudo-oculto');
            this.form.style.display = 'grid';
            this._resetarFormulario();
        } else {
            this.form.classList.add('conteudo-oculto');
            setTimeout(() => { this.form.style.display = 'none'; }, 300);
        }
    }

    _resetarFormulario() {
        this.form.reset();
        document.getElementById('action').value = 'add';
        document.getElementById('livro_id').value = '';
        document.getElementById('btn-menu-criar').innerText = "Salvar Livro";
        if(document.getElementById('existingPdf')) document.getElementById('existingPdf').innerText = '';
        
        // Limpa Tom Selects
        Object.values(this.tomSelects).forEach(tom => { if(tom) tom.clear(); });
    }

    /* --- AUXILIARES --- */

    _inicializarTomSelect(id, dados, keyId, keyNome, isMultiple) {
        // Destroi instância anterior se existir para evitar duplicação
        if (this.tomSelects[id]) {
            this.tomSelects[id].destroy();
            this.tomSelects[id] = null;
        }

        const select = document.getElementById(id);
        if (!select) return;

        select.innerHTML = isMultiple ? '' : '<option value="">Selecione...</option>';
        dados.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[keyId];
            opt.text = item[keyNome];
            select.appendChild(opt);
        });

        // Cria e salva a instância
        this.tomSelects[id] = new TomSelect(`#${id}`, {
            plugins: isMultiple ? ['remove_button'] : [],
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: "Selecione..."
        });
    }

    _renderizarPaginacao(totalPaginas) {
        if(!this.paginacaoContainer) return;
        this.paginacaoContainer.innerHTML = '';
        if(totalPaginas <= 1) return;

        const criarBotao = (texto, page, ativo = false) => {
            const btn = document.createElement('button');
            btn.innerText = texto;
            if(ativo) btn.classList.add('ativo');
            btn.onclick = () => { this.paginaAtual = page; this.filtrarEAtualizar(); };
            this.paginacaoContainer.appendChild(btn);
        };

        // Botão Anterior
        if(this.paginaAtual > 1) criarBotao('<', this.paginaAtual - 1);

        // Lógica simplificada de paginação (exibindo números próximos)
        let start = Math.max(1, this.paginaAtual - 2);
        let end = Math.min(totalPaginas, this.paginaAtual + 2);

        for(let i = start; i <= end; i++) {
            criarBotao(i, i, i === this.paginaAtual);
        }

        // Botão Próximo
        if(this.paginaAtual < totalPaginas) criarBotao('>', this.paginaAtual + 1);
    }

    _setupPdfInput() {
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
    }
}