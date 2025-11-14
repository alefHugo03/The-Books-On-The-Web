import {validarData} from "../validations/data.js";
import {validarEmail} from "../validations/email.js";
import {validarNome} from "../validations/name.js";
import {validarSenha, validarConfirmarSenha} from "../validations/password.js";
import {validarCpf, barraCpf} from "../validations/cpf.js"
import {etapa, limparAviso, avisoFalas} from "/The-Books-On-The-Web/public/scripts/validations/utilits.js";
const formCadastro = document.getElementById("form-cadastro");


/* Pagina de cadastro  */
formCadastro.addEventListener('submit', processarDadosCadastro);

barraCpf('cpf')

function processarDadosCadastro(event) {
    event.preventDefault(); 
    console.log("Formulário interceptado pelo JS.");

    etapa.forEach(limparAviso);

    const nome = validarNome('nome');
    const email = validarEmail('email');
    const nascimento = validarData('data');
    const senha = validarSenha('senha');
    const confirmarSenha = validarConfirmarSenha(senha, 'senhaDois');
    const confirmarCpf = validarCpf('cpf');


    if (!nome || !email || !nascimento || !senha || !confirmarSenha || !confirmarCpf) return;

    const dados = new FormData(formCadastro);

    fetch('/The-Books-On-The-Web/public/api/login/cadastro.php', {
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






