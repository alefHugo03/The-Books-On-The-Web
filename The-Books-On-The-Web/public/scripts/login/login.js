import {validarEmail} from "../validations/email.js";
import {validarSenha} from "../validations/password.js";

// Pega o formulário pelo ID
const formLogin = document.getElementById("form-login");

formLogin.addEventListener('submit', processarDadosLogin);

function processarDadosLogin(event) {
    event.preventDefault(); 
    console.log("Formulário interceptado pelo JS.");

    const email = validarEmail('emailEntrar');
    const senha = validarSenha('senhaEntrar');
    
    if (!email || !senha) return;

    const dados = new FormData(formLogin); 

    fetch('/The-Books-On-The-Web/public/src/login/processar_login.php', {
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
}
