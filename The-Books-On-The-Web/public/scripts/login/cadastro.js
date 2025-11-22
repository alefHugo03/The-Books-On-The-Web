import FormValidator from "../classes/FormValidator.js";
import { barraCpf } from "../validations/cpf.js";
import { avisoFalas, etapa } from "../validations/utilits.js";

const formCadastro = document.getElementById("form-cadastro");
const validador = new FormValidator();

document.addEventListener("DOMContentLoaded", () => {
    barraCpf('cpf'); // Ativa máscara
});

if (formCadastro) {
    formCadastro.addEventListener('submit', (event) => {
        event.preventDefault();
        
        // Caminho corrigido para FormValidator
        const vNome = validador.validarCampo('nome', 'avisoNome', ['obrigatorio', 'min:3', 'nome']);
        const vEmail = validador.validarCampo('email', 'avisoEmail', ['obrigatorio', 'email']);
        const vCpf = validador.validarCampo('cpf', 'avisoCpf', ['obrigatorio', 'cpf']);
        const vData = validador.validarCampo('data', 'avisoData', ['obrigatorio', 'data']);
        const vSenha = validador.validarCampo('senha', 'avisoSenha', ['obrigatorio', 'min:6']);
        const vSenha2 = validador.validarConfirmacaoSenha('senha', 'senhaDois', 'avisoSenhaDois');

        if (!vNome || !vEmail || !vCpf || !vData || !vSenha || !vSenha2) return;

        const dados = new FormData(formCadastro);
        // Caminho absoluto para API (seguro)
        fetch('/The-Books-On-The-Web/public/api/login/cadastro.php', { method: 'POST', body: dados })
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) window.location.href = data.redirect_url;
            else avisoFalas(data.mensagem, etapa[0]);
        })
        .catch(() => avisoFalas("Erro de conexão.", etapa[0]));
    });
}