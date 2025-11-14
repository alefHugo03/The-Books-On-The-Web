import { validarData } from "../validations/data.js";
import { validarEmail } from "../validations/email.js";
import { validarNome } from "../validations/name.js";
import { validarSenha, validarConfirmarSenha } from "../validations/password.js";
import { validarCpf, barraCpf } from "../validations/cpf.js";
import { etapa, limparAviso, avisoFalas } from "../validations/utilits.js"; // Ajuste o caminho se necessário

const formCadastro = document.getElementById("form-cadastro");

document.addEventListener("DOMContentLoaded", function() {
    carregarUsuarios();
    barraCpf('cpfAdmin');
});

if (formCadastro) {
    formCadastro.addEventListener('submit', processarDadosCadastro);
}

function processarDadosCadastro(event) {
    event.preventDefault(); 
    console.log("Formulário Admin interceptado pelo JS.");

    etapa.forEach(limparAviso);
    limparAviso('avisoTipo'); 
    limparAviso('aviso');  

    const nome = validarNome('nomeAdmin');
    const email = validarEmail('emailAdmin');
    const nascimento = validarData('dataAdmin');
    const senha = validarSenha('senhaAdmin');
    const confirmarSenha = validarConfirmarSenha(senha, 'senhaAdminDois');
    const confirmarCpf = validarCpf('cpfAdmin');

    const tipoInput = document.getElementById('tipo');
    let tipoValido = true;
    if (!tipoInput.value) {
        avisoFalas("Selecione o tipo de conta.", "avisoTipo"); // Usa avisoFalas para manter padrão
        tipoValido = false;
    }

    if (!nome || !email || !nascimento || !senha || !confirmarSenha || !confirmarCpf || !tipoValido) return;

    const dados = new FormData(formCadastro);

    fetch('api/login/admin/gerenciarUsuarios.php', {
        method: 'POST',
        body: dados 
    })
    .then(response => response.json()) 
    .then(data => {
        console.log("Resposta do servidor:", data);
        
        if (data.success) {
            alert(data.message);
            formCadastro.reset();
            carregarUsuarios();
        } else {
            avisoFalas(data.message, "aviso"); 
        }
    })
    .catch(error => {
        console.error("Erro no fetch:", error);
        avisoFalas("Erro de conexão. Tente mais tarde.", "aviso");
    });
};

/* --- LÓGICA DA TABELA (Exclusiva do Painel) --- */

function carregarUsuarios() {
    fetch('api/login/admin/gerenciarUsuarios.php', { method: 'GET' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            preencherTabela('tbody-ativos', data.ativos, true);
            preencherTabela('tbody-inativos', data.inativos, false);
        }
    })
    .catch(error => console.error("Erro ao carregar usuários:", error));
}

function preencherTabela(idTbody, listaUsuarios, isAtivo) {
    const tbody = document.getElementById(idTbody);
    if (!tbody) return;

    tbody.innerHTML = ''; 

    if (!listaUsuarios || listaUsuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Nenhum usuário encontrado.</td></tr>';
        return;
    }

    listaUsuarios.forEach(user => {
        const tr = document.createElement('tr');
        
        let botoes = '';
        if (isAtivo) {
            botoes = `<button type="button" onclick="alterarStatusUsuario(${user.id_user}, 'delete')" class="btn-delete">Desativar</button>`;
        } else {
            botoes = `
                <button type="button" onclick="alterarStatusUsuario(${user.id_user}, 'activate')" class="btn-small">Ativar</button> 
                <button type="button" onclick="alterarStatusUsuario(${user.id_user}, 'delete_permanent')" class="btn-delete" style="background:darkred;">Excluir</button>
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

// Função Global para os botões da tabela funcionarem
window.alterarStatusUsuario = function(id, action) {
    let msg = action === 'delete_permanent' ? "Tem certeza que deseja EXCLUIR PERMANENTEMENTE?" : "Confirmar ação?";
    if (!confirm(msg)) return;

    const formData = new FormData();
    formData.append('action', action);
    formData.append('id_user', id);

    fetch('api/login/admin/gerenciarUsuarios.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        carregarUsuarios();
    })
    .catch(err => console.error(err));
};