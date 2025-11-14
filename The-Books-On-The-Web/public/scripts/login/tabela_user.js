// 1. IMPORTS DAS VALIDAÇÕES (Trazidos do seu cadastroAdmin.js)
// Ajuste os caminhos conforme sua estrutura de pastas real
import { validarData } from "../validations/data.js";
import { validarEmail } from "../validations/email.js";
import { validarNome } from "../validations/name.js";
import { validarSenha, validarConfirmarSenha } from "../validations/password.js";
import { validarCpf, barraCpf} from "../validations/cpf.js";
import { validarTipo } from "../validations/tipo.js";
import { etapa, limparAviso, avisoFalas } from "../validations/utilits.js";

// Caminho da API
const API_URL = 'api/login/admin/gerenciarUsuarios.php';

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    carregarUsuarios();
    configurarFormulario();
});

// --- FUNÇÕES DE TABELA (Mantidas do anterior) ---
async function carregarUsuarios() {
    try {
        const response = await fetch(API_URL);
        const data = await response.json();

        if (data.success) {
            preencherTabela('tbody-ativos', data.ativos, true);
            preencherTabela('tbody-inativos', data.inativos, false);
        }
    } catch (error) {
        console.error('Erro na requisição:', error);
    }
}

function preencherTabela(tbodyId, usuarios, isAtivo) {
    const tbody = document.getElementById(tbodyId);
    tbody.innerHTML = '';

    if (!usuarios || usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5">Nenhum usuário encontrado.</td></tr>';
        return;
    }

    usuarios.forEach(user => {
        const tr = document.createElement('tr');
        let botoesHTML = '';
        
        if (isAtivo) {
            botoesHTML = `
                <button onclick="editarUsuario(${user.id_user})" class="btn-menu btn-edit">Editar</button>
                <button onclick="alterarStatus(${user.id_user}, 'delete')" class="btn-menu btn-desativar">Desativar</button>
            `;
        } else {
            botoesHTML = `
                <button onclick="alterarStatus(${user.id_user}, 'activate')" class="btn-menu btn-success">Ativar</button>
                <button onclick="alterarStatus(${user.id_user}, 'delete_permanent')" class="btn-menu btn-delete-perm">Deletar</button>
            `;
        }

        tr.innerHTML = `
            <td>${user.id_user}</td>
            <td>${user.nome}</td>
            <td>${user.email}</td>
            <td>${user.tipo}</td>
            <td>${botoesHTML}</td>
        `;
        tbody.appendChild(tr);
    });
}

// --- LÓGICA DO FORMULÁRIO (INTEGRADA COM VALIDAÇÃO) ---
function configurarFormulario() {
    const form = document.getElementById('form-cadastro');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // Impede recarregamento
        
        // 1. Limpa avisos anteriores
        etapa.forEach(limparAviso);

        barraCpf('cpfAdmin');
        // 2. Executa suas Validações
        // Certifique-se que os IDs no HTML (ex: 'nomeAdmin') batem com o que as funções esperam
        const nomeValido = validarNome('nomeAdmin');
        const emailValido = validarEmail('emailAdmin');
        const cpfValido = validarCpf('cpfAdmin');
        const dataValida = validarData('dataAdmin');
        const senhaValida = validarSenha('senhaAdmin');
        
        // Para confirmar senha, precisamos passar o valor da primeira senha
        // Assumindo que validarConfirmarSenha pega o valor pelo ID 'senhaAdminDois'
        // Se sua função validarConfirmarSenha espera (senhaValue, idCampoConfirmacao):
        const senhaValue = document.getElementById('senhaAdmin').value;
        const confSenhaValida = validarConfirmarSenha(senhaValue, 'senhaAdminDois'); 
        
        const tipoValido = validarTipo('tipo');

        


        // 3. Se algum falhar, para o envio aqui
        if (!nomeValido || !emailValido || !cpfValido || !dataValida || !senhaValida || !confSenhaValida || !tipoValido) {
            console.log("Bloqueado pela validação do JS");
            return;
        }

        // 4. Se passou na validação, envia para a API
        const formData = new FormData(form);

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            // Exibe feedback visual
            mostrarFeedback(result.message, result.success);

            if (result.success) {
                form.reset();       // Limpa os campos
                carregarUsuarios(); // Atualiza a tabela sem F5
            } else {
                // Se o erro veio do PHP (ex: email duplicado), mostra no feedback geral ou no campo específico se possível
                avisoFalas(result.message, etapa[0]); 
            }
        } catch (error) {
            console.error("Erro no fetch:", error);
            mostrarFeedback("Erro de conexão com o servidor.", false);
        }
    });
}

// --- FUNÇÕES GLOBAIS (Ações da tabela) ---
window.alterarStatus = async (id, action) => {
    let texto = (action === 'delete_permanent') ? "Excluir para SEMPRE?" : "Tem certeza?";
    if (!confirm(texto)) return;

    const formData = new FormData();
    formData.append('action', action);
    formData.append('id_user', id);

    try {
        const response = await fetch(API_URL, { method: 'POST', body: formData });
        const result = await response.json();
        mostrarFeedback(result.message, result.success);
        if (result.success) carregarUsuarios();
    } catch (error) { console.error(error); }
};

window.editarUsuario = (id) => {
    window.location.href = `api/login/admin/editar_usuario.php?id=${id}`;
};

function mostrarFeedback(msg, sucesso) {
    const div = document.getElementById('feedback-msg');
    if (!div) return;
    div.style.display = 'block';
    div.style.backgroundColor = sucesso ? '#d4edda' : '#f8d7da';
    div.style.color = sucesso ? '#155724' : '#721c24';
    div.innerText = msg;
    setTimeout(() => { div.style.display = 'none'; }, 4000);
}