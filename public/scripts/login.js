/*  Pagina principal  */
import {validarEmail} from "./validations/email.js";
import {validarSenha} from "./validations/password.js";



const btmEntrar = document.getElementById("btnEntrar");
const entradaEmail = document.getElementById("emailEntrar");
const entradaSenha = document.getElementById("senhaEntrar");
const etapa = ["avisoEmail", "avisoSenha"];

btmEntrar.addEventListener('click', processarDadosLogin);


function processarDadosLogin() {
    const senha = validarSenha();
    const email = validarEmail();
    
    if (!email && !senha) return;

    alert("Enviado!")
}

const validarEmail = () => {
    const email = entradaEmail.value;
    const avisoElement =  document.getElementById("avisoEmail");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    nomeFalas = ["Digite um email válido", "O campo não pode estar vazio."]

    if (email === "") return avisoFalas(nomeFalas[1], etapa[0]);
    if (!emailRegex.test(email)) return avisoFalas(nomeFalas[0], etapa[0]);
        
    avisoElement.innerHTML = ""; 
    avisoElement.classList.remove('aviso-ativo');
    return email;
};


const validarSenha = () => {
    const senha = entradaSenha.value; 
    const avisoElement = document.getElementById("avisoSenha");
    nomeFalas = ["Digite uma senha válida", "O campo não pode estar vazio."]

    if (senha === "") return avisoFalas(nomeFalas[1], etapa[1]);
    if (senha.length < 6) return avisoFalas(nomeFalas[0], etapa[1]);

    avisoElement.innerHTML = ""; 
    avisoElement.classList.remove('aviso-ativo');
    
    return senha;
};

const avisoFalas = (fala , etapa) => {
    const avisoElement = document.getElementById(etapa);

    avisoElement.innerHTML = fala;
    avisoElement.classList.add('aviso-ativo');
    setTimeout(() => { 
        avisoElement.classList.remove('aviso-ativo');
        avisoElement.innerHTML = ""; 
    }, 4000);
    return;
};