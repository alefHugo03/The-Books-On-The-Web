import {validarData} from "../validations/data.js";
import {validarEmail} from "../validations/email.js";
import {validarNome} from "../validations/name.js";
import {validarSenha, validarConfirmarSenha} from "../validations/password.js";
import {validarCpf} from "../validations/cpf.js"
import {etapa, limparAviso} from "/ProjetoM2/The-Books-On-The-Web/public/scripts/validations/utilits.js";
const formCadastro = document.getElementById("form-cadastro");

/* Pagina de cadastro  */
formCadastro.addEventListener('submit', processarDadosCadastro);

function processarDadosCadastro(event) {
    event.preventDefault(); 
    console.log("Formulário interceptado pelo JS.");

    etapa.forEach(limparAviso);

    const nome = validarNome();
    const email = validarEmail();
    const nascimento = validarData();
    const senha = validarSenha();
    const confirmarSenha = validarConfirmarSenha(senha);
    const confirmarCpf = validarCpf();


    if (!nome || !email || !nascimento || !senha || !confirmarSenha || !confirmarCpf) return;

    const dados = new FormData(formCadastro);

    fetch('/ProjetoM2/The-Books-On-The-Web/public/src/login/cadastro.php', {
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






