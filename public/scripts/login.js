/*  Pagina principal  */
import {validarEmail} from "./validations/email.js";
import {validarSenha} from "./validations/password.js";



const btmEntrar = document.getElementById("btnEntrar");

btmEntrar.addEventListener('click', processarDadosLogin);


function processarDadosLogin() {
    const senha = validarSenha();
    const email = validarEmail();
    
    if (!email && !senha) return;

    alert("Enviado!")
}