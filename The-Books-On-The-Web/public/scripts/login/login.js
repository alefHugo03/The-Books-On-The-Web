import { validarEmail } from "../validations/email.js";
import { validarSenha } from "../validations/password.js";
import { limparAviso, avisoFalas } from "../validations/utilits.js";

document.addEventListener("DOMContentLoaded", () => {
    console.log("Login.js carregado!");
    const formLogin = document.getElementById("form-login");

    if (formLogin) {
        formLogin.addEventListener('submit', processarDadosLogin);
    }
});

function processarDadosLogin(event) {
    event.preventDefault(); 
    
    console.log("--- INICIANDO VALIDAÇÃO ---");

    // 1. Verifica se consegue ler os campos
    const emailInput = document.getElementById('emailEntrar');
    const senhaInput = document.getElementById('senhaEntrar');

    if (!emailInput || !senhaInput) {
        alert("ERRO CRÍTICO: O JavaScript não encontrou os campos de input no HTML. Verifique os IDs 'emailEntrar' e 'senhaEntrar'.");
        return;
    }

    console.log("Valor Email digitado:", emailInput.value);
    console.log("Valor Senha digitada:", senhaInput.value);

    // 2. Limpa avisos
    limparAviso('avisoEmail');
    limparAviso('avisoSenha');
    limparAviso('aviso');

    // 3. Executa validações e mostra o motivo se falhar
    const emailValido = validarEmail('emailEntrar');
    if (!emailValido) {
        console.log("Erro: Validação de Email retornou falso.");
        // O próprio validarEmail já deve ter mostrado a mensagem vermelha na tela
    }

    const senhaValida = validarSenha('senhaEntrar');
    if (!senhaValida) {
        console.log("Erro: Validação de Senha retornou falso.");
    }
    
    if (!emailValido || !senhaValida) {
        console.warn("Validação falhou no front-end. O envio foi bloqueado.");
        return; // PARA AQUI SE TIVER ERRO
    }

    // 4. Se passou, envia
    console.log("Validação OK! Enviando para o PHP...");
    const dados = new FormData(event.target); 

    fetch('/The-Books-On-The-Web/public/api/login/processar_login.php', {
        method: 'POST',
        body: dados 
    })
    .then(response => response.json()) 
    .then(data => {
        console.log("Resposta do PHP:", data);
        
        if (data.sucesso) {
            window.location.href = data.redirect_url;
        } else {
            avisoFalas(data.mensagem, "aviso"); 
        }
    })
    .catch(error => {
        console.error("Erro no fetch:", error);
        avisoFalas("Erro de conexão com o servidor.", "aviso");
    });
}