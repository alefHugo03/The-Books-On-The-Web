import { avisoFalas, limparAviso, etapa } from "../validations/utilits.js";
// Importamos os validadores antigos apenas se forem usados dentro da classe, 
// mas agora usaremos o FormValidator injetado ou criado aqui.
import FormValidator from "./FormValidator.js";

export default class GerenciadorAdmin {
    constructor(config) {
        this.apiUrl = config.apiUrl;
        this.formCadastro = document.getElementById(config.formId);
        this.tbodyAtivos = document.getElementById(config.tableAtivosId);
        this.tbodyInativos = document.getElementById(config.tableInativosId);
        this.selectTipo = document.getElementById(config.tipoSelectId);
        
        this.validator = new FormValidator();

        // Bindings
        this.processarCadastro = this.processarCadastro.bind(this);
        this.gerenciarCliquesTabela = this.gerenciarCliquesTabela.bind(this);
    }

    init() {
        console.log("Admin Manager: Iniciado");
        this.carregarUsuarios();
        
        if (this.formCadastro) this.formCadastro.addEventListener('submit', this.processarCadastro);
        if (this.tbodyAtivos) this.tbodyAtivos.addEventListener('click', this.gerenciarCliquesTabela);
        if (this.tbodyInativos) this.tbodyInativos.addEventListener('click', this.gerenciarCliquesTabela);
    }

    carregarUsuarios() {
        fetch(this.apiUrl, { method: 'GET' })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this._renderizarTabela(this.tbodyAtivos, data.ativos, true);
                    this._renderizarTabela(this.tbodyInativos, data.inativos, false);
                }
            })
            .catch(err => console.error("Erro ao carregar usuários:", err));
    }

    processarCadastro(event) {
        event.preventDefault();
        etapa.forEach(limparAviso);

        // Validação usando a Classe FormValidator
        const vNome = this.validator.validarCampo('nomeAdmin', 'avisoNome', ['obrigatorio', 'nome']);
        const vEmail = this.validator.validarCampo('emailAdmin', 'avisoEmail', ['obrigatorio', 'email']);
        const vCpf = this.validator.validarCampo('cpfAdmin', 'avisoCpf', ['obrigatorio', 'cpf']);
        const vData = this.validator.validarCampo('dataAdmin', 'avisoData', ['obrigatorio', 'data']);
        const vSenha = this.validator.validarCampo('senhaAdmin', 'avisoSenha', ['obrigatorio', 'min:6']);
        const vSenha2 = this.validator.validarConfirmacaoSenha('senhaAdmin', 'senhaAdminDois', 'avisoSenhaDois');

        let vTipo = true;
        if (!this.selectTipo.value) { avisoFalas("Selecione o tipo.", "avisoTipo"); vTipo = false; }

        if (!vNome || !vEmail || !vCpf || !vData || !vSenha || !vSenha2 || !vTipo) return;

        const dados = new FormData(this.formCadastro);
        fetch(this.apiUrl, { method: 'POST', body: dados })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                this.formCadastro.reset();
                this.carregarUsuarios();
            } else {
                avisoFalas(data.message, "aviso");
            }
        })
        .catch(() => avisoFalas("Erro de conexão.", "aviso"));
    }

    alterarStatus(id, action) {
        const msg = action === 'delete_permanent' ? "EXCLUIR PERMANENTEMENTE?" : "Confirmar ação?";
        if (!confirm(msg)) return;

        const fd = new FormData();
        fd.append('action', action);
        fd.append('id_user', id);

        fetch(this.apiUrl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            this.carregarUsuarios();
        });
    }

    _renderizarTabela(tbody, lista, isAtivo) {
        tbody.innerHTML = '';
        if (!lista || lista.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" align="center">Nenhum usuário.</td></tr>';
            return;
        }
        lista.forEach(u => {
            const tr = document.createElement('tr');
            let btnHtml = isAtivo 
                ? `<button class="btn-delete acao-btn" data-id="${u.id_user}" data-act="delete">Desativar</button>`
                : `<button class="btn-small acao-btn" data-id="${u.id_user}" data-act="activate">Ativar</button> 
                   <button class="btn-delete acao-btn" data-id="${u.id_user}" data-act="delete_permanent">Excluir</button>`;
            
            tr.innerHTML = `<td>${u.id_user}</td><td>${u.nome}</td><td>${u.email}</td><td>${u.tipo}</td><td>${btnHtml}</td>`;
            tbody.appendChild(tr);
        });
    }

    gerenciarCliquesTabela(e) {
        if (e.target.classList.contains('acao-btn')) {
            this.alterarStatus(e.target.dataset.id, e.target.dataset.act);
        }
    }
}