import { validarData } from "../validations/data.js";
import { validarEmail } from "../validations/email.js";
import { validarNome } from "../validations/name.js";
import { validarSenha, validarConfirmarSenha } from "../validations/password.js";
import { validarCpf, barraCpf } from "../validations/cpf.js";
import { etapa, limparAviso, avisoFalas } from "../validations/utilits.js";

export default class GerenciadorAdmin {
    /**
     * Construtor: Define as configurações iniciais e elementos do DOM.
     * @param {Object} config - Objeto com IDs e URLs da API.
     */
    constructor(config) {
        // Configurações (URLs e IDs)
        this.apiUrl = config.apiUrl || 'api/login/admin/gerenciarUsuarios.php';
        
        // Elementos do DOM
        this.formCadastro = document.getElementById(config.formId);
        this.tbodyAtivos = document.getElementById(config.tableAtivosId);
        this.tbodyInativos = document.getElementById(config.tableInativosId);
        this.selectTipo = document.getElementById(config.tipoSelectId);

        // Vincula o contexto do 'this' para métodos que são chamados por eventos
        this.processarCadastro = this.processarCadastro.bind(this);
        this.gerenciarCliquesTabela = this.gerenciarCliquesTabela.bind(this);
    }

    /**
     * Método Inicializador: Coloca tudo para rodar.
     */
    init() {
        console.log("Gerenciador Admin: Iniciado (POO)");
        
        // 1. Carregar dados iniciais
        this.carregarUsuarios();
        
        // 2. Ativar máscara de CPF
        barraCpf('cpfAdmin');

        // 3. Escutar envio do formulário
        if (this.formCadastro) {
            this.formCadastro.addEventListener('submit', this.processarCadastro);
        }

        // 4. Escutar cliques nas tabelas (Event Delegation)
        // Isso substitui o onclick no HTML, deixando o código mais limpo
        if (this.tbodyAtivos) this.tbodyAtivos.addEventListener('click', this.gerenciarCliquesTabela);
        if (this.tbodyInativos) this.tbodyInativos.addEventListener('click', this.gerenciarCliquesTabela);
    }

    /* --- MÉTODOS DE LÓGICA (CRUD) --- */

    carregarUsuarios() {
        fetch(this.apiUrl, { method: 'GET' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this._renderizarTabela(this.tbodyAtivos, data.ativos, true);
                    this._renderizarTabela(this.tbodyInativos, data.inativos, false);
                }
            })
            .catch(error => console.error("Erro ao carregar usuários:", error));
    }

    processarCadastro(event) {
        event.preventDefault();
        console.log("POO: Processando cadastro...");

        etapa.forEach(limparAviso);
        limparAviso('avisoTipo');
        limparAviso('aviso');

        // Validações
        const validacoes = [
            validarNome('nomeAdmin'),
            validarEmail('emailAdmin'),
            validarData('dataAdmin'),
            validarSenha('senhaAdmin'),
            validarConfirmarSenha(document.getElementById('senhaAdmin').value, 'senhaAdminDois'),
            validarCpf('cpfAdmin')
        ];

        // Validação específica do select (que não tem arquivo próprio)
        let tipoValido = true;
        if (!this.selectTipo.value) {
            avisoFalas("Selecione o tipo de conta.", "avisoTipo");
            tipoValido = false;
        }

        // Se algum for false, para aqui.
        if (validacoes.includes(false) || !tipoValido) return;

        const dados = new FormData(this.formCadastro);

        fetch(this.apiUrl, {
            method: 'POST',
            body: dados
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                this.formCadastro.reset();
                this.carregarUsuarios(); // Recarrega a tabela
            } else {
                avisoFalas(data.message, "aviso");
            }
        })
        .catch(err => {
            console.error(err);
            avisoFalas("Erro de conexão.", "aviso");
        });
    }

    alterarStatus(id, action) {
        const msg = action === 'delete_permanent' 
            ? "Tem certeza que deseja EXCLUIR PERMANENTEMENTE?" 
            : "Confirmar ação?";
        
        if (!confirm(msg)) return;

        const formData = new FormData();
        formData.append('action', action);
        formData.append('id_user', id);

        fetch(this.apiUrl, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            this.carregarUsuarios(); // Atualiza a visualização
        })
        .catch(err => console.error(err));
    }

    /* --- MÉTODOS DE RENDERIZAÇÃO (PRIVADOS/AUXILIARES) --- */

    /**
     * Gera o HTML da tabela. Note que usamos data-attributes nos botões
     * em vez de onclick="funcaoGlobal()".
     */
    _renderizarTabela(tbody, listaUsuarios, isAtivo) {
        tbody.innerHTML = '';

        if (!listaUsuarios || listaUsuarios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Nenhum usuário encontrado.</td></tr>';
            return;
        }

        listaUsuarios.forEach(user => {
            const tr = document.createElement('tr');
            let botoes = '';

            if (isAtivo) {
                botoes = `<button type="button" class="btn-delete acao-btn" data-id="${user.id_user}" data-action="delete">Desativar</button>`;
            } else {
                botoes = `
                    <button type="button" class="btn-small acao-btn" data-id="${user.id_user}" data-action="activate">Ativar</button> 
                    <button type="button" class="btn-delete acao-btn" style="background:darkred;" data-id="${user.id_user}" data-action="delete_permanent">Excluir</button>
                `;
            }

            tr.innerHTML = `
                <td>${user.id_user}</td>
                <td>${user.nome}</td>
                <td>${user.email}</td>
                <td>${user.tipo}</td>
                <td>${botoes}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    /**
     * Event Delegation: Identifica qual botão foi clicado dentro da tabela
     */
    gerenciarCliquesTabela(event) {
        const elemento = event.target;
        
        // Verifica se o clique foi em um botão com a classe 'acao-btn'
        if (elemento.classList.contains('acao-btn')) {
            const id = elemento.dataset.id;
            const action = elemento.dataset.action;
            this.alterarStatus(id, action);
        }
    }
}