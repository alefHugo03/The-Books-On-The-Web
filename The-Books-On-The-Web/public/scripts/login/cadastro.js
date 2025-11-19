// ARQUIVO: public/scripts/cadastro.js
import FormValidator from "./classes/FormValidator.js"; // Importa a nova classe de validação
import { barraCpf } from "./validations/cpf.js";        // Mantém a máscara visual separada
import { avisoFalas, etapa } from "./validations/utilits.js"; // Para avisos de erro do servidor

const formCadastro = document.getElementById("form-cadastro");

// 1. Instancia o validador
const validador = new FormValidator();

document.addEventListener("DOMContentLoaded", () => {
    // 2. Ativa a máscara de CPF (apenas visual)
    barraCpf('cpf');
});

// 3. Evento de Envio
if (formCadastro) {
    formCadastro.addEventListener('submit', processarDadosCadastro);
}

function processarDadosCadastro(event) {
    event.preventDefault(); 
    console.log("Cadastro: Iniciando validação via Classe...");

    // --- VALIDAÇÃO COM A NOVA CLASSE ---
    // Sintaxe: validarCampo(ID_INPUT, ID_AVISO, [REGRAS])
    
    const vNome = validador.validarCampo('nome', 'avisoNome', ['obrigatorio', 'min:3', 'nome']);
    const vEmail = validador.validarCampo('email', 'avisoEmail', ['obrigatorio', 'email']);
    const vData = validador.validarCampo('data', 'avisoData', ['obrigatorio', 'data']);
    const vSenha = validador.validarCampo('senha', 'avisoSenha', ['obrigatorio', 'min:6']);
    const vCpf = validador.validarCampo('cpf', 'avisoCpf', ['obrigatorio', 'cpf']);
    
    // Validação especial de comparação de senhas
    const vSenha2 = validador.validarConfirmacaoSenha('senha', 'senhaDois', 'avisoSenhaDois');

    // Se qualquer um falhar (retornar false), interrompe o envio
    if (!vNome || !vEmail || !vData || !vSenha || !vCpf || !vSenha2) {
        return;
    }

    // --- ENVIO PARA O SERVIDOR ---
    console.log("Validação frontend OK. Enviando...");
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
            // Usa o utilitário antigo para mostrar erro que veio do PHP (ex: "Email já existe")
            // etapa[0] geralmente é o aviso genérico ou do nome, ajuste conforme sua lógica de erro do backend
            avisoFalas(data.mensagem, etapa[0]); 
        }
    })
    .catch(error => {
        console.error("Erro no fetch:", error);
        avisoFalas("Erro de conexão. Tente mais tarde.", etapa[0]);
    });
}