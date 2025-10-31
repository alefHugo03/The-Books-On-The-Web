import {validarData} from "./validations/data.js";
import {validarEmail} from "./validations/email.js";
import {validarNome} from "./validations/name.js";
import {validarSenha, validarConfirmarSenha} from "./validations/password.js";
import {validarCpf} from "./validations/cpf.js"
import {etapa, limparAviso} from "./validations/utilits.js";

/* Pagina de cadastro  */
const btmCriar = document.getElementById("btn-menu-criar");

btmCriar.addEventListener("click", processarDadosCadastro)

function processarDadosCadastro() {
    
    etapa.forEach(limparAviso);

    const nome = validarNome();
    const email = validarEmail();
    const nascimento = validarData();
    const senha = validarSenha();
    const confirmarSenha = validarConfirmarSenha(senha);
    const confirmarCpf = validarCpf();


    if (!nome || !email || !nascimento || !senha || !confirmarSenha || confirmarCpf) return; 


    alert("Enviado!")
};






