import {validarData} from "../validations/data.js";
import {validarEmail} from "../validations/email.js";
import {validarNome} from "../validations/name.js";
import {validarSenha, validarConfirmarSenha} from "../validations/password.js";
import {validarCpf} from "../validations/cpf.js"
import {validarTipo} from "../validations/tipo.js"
import {etapa, limparAviso} from "/The-Books-On-The-Web/public/scripts/validations/utilits.js";

const formCadastro = document.getElementById("form-cadastro");

/* Pagina de cadastro  */
formCadastro.addEventListener('submit', processarDadosCadastro);

function processarDadosCadastro(event) {
    event.preventDefault(); 
    console.log("Formulário interceptado pelo JS.");

    etapa.forEach(limparAviso);

    const nome = validarNome('nomeAdmin');
    const email = validarEmail('emailAdmin');
    const nascimento = validarData('dataAdmin');
    const senha = validarSenha('senhaAdmin');
    const confirmarSenha = validarConfirmarSenha(senha, 'senhaAdminDois');
    const confirmarCpf = validarCpf('cpfAdmin');
    const confirmarTipo = validarTipo('tipo');


if (!nome || !email || !nascimento || !senha || !confirmarSenha || !confirmarCpf || !confirmarTipo) return;
    const dados = new FormData(formCadastro);

    fetch('/The-Books-On-The-Web/public/src/login/cadastroAdmin.php', {
        method: 'POST',
        body: dados 
    })
    .then(response => response.json()) 
    .then(data => {
        console.log("Resposta do servidor:", data);
        
        if (data.sucesso) {
            window.location.href = data.redirect_url;
        } else {
            avisoFalas(data.mensagem, etapa[0]); 
        }
    })
    .catch(error => {
        console.error("Erro no fetch:", error);
        avisoFalas("Erro de conexão. Tente mais tarde.", etapa[0]);
    });
};






