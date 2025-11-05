import {validarData} from "../validations/data.js";
import {validarEmail} from "../validations/email.js";
import {validarNome} from "../validations/name.js";
import {validarSenha, validarConfirmarSenha} from "../validations/password.js";
import {validarCpf} from "../validations/cpf.js"
import {etapa, limparAviso} from "/ProjetoM2/The-Books-On-The-Web/public/scripts/validations/utilits.js";

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

    // 4. Envia para o servidor
    const dadosParaEnviar = {
        nome: nome, 
        email: email,
        nascimento: nascimento,
        senha: senha
    };

    console.log("Dados validados, enviando:", dadosParaEnviar);

    fetch('/cadastro', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dadosParaEnviar),
    })
    .then(response => {
        if (response.ok) {
            alert("Cadastro realizado com sucesso!");
        } else {
            alert("Ocorreu um erro no cadastro. Tente novamente.");
        }
    })
    .catch(error => {
        console.error("Erro na requisição:", error);
        alert("Não foi possível conectar ao servidor.");
    });
};






