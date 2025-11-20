import { avisoFalas, limparAviso } from "../validations/utilits.js";

export default class FormValidator {
    constructor() {
        this.regex = {
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            nome: /^[a-zA-Z\s]+$/,
            cpf: /^\d{11}$/
        };
    }

    validarCampo(inputId, avisoId, regras) {
        const input = document.getElementById(inputId);
        if (!input) return false;

        const valor = input.value.trim();
        limparAviso(avisoId);

        for (let regra of regras) {
            if (regra === 'obrigatorio' && valor === "") {
                this._mostrarErro("Este campo não pode estar vazio.", avisoId, input);
                return false;
            }

            if (regra === 'email' && !this.regex.email.test(valor)) {
                this._mostrarErro("Por favor, insira um email válido.", avisoId, input);
                return false;
            }

            if (regra.startsWith('min:')) {
                const min = parseInt(regra.split(':')[1]);
                if (valor.length < min) {
                    this._mostrarErro(`Mínimo de ${min} caracteres.`, avisoId, input);
                    return false;
                }
            }

            if (regra === 'nome' && !this.regex.nome.test(valor)) {
                this._mostrarErro("Apenas letras são permitidas.", avisoId, input);
                return false;
            }

            if (regra === 'data' && !this._validarDataNascimento(valor, avisoId, input)) return false;

            if (regra === 'cpf') {
                const cpfLimpo = valor.replace(/\D/g, '');
                if (cpfLimpo.length !== 11) {
                    this._mostrarErro("O CPF deve conter 11 dígitos.", avisoId, input);
                    return false;
                }
            }
        }
        return true;
    }

    validarConfirmacaoSenha(idSenha, idConfirmacao, avisoId) {
        const senha = document.getElementById(idSenha)?.value;
        const confirmacao = document.getElementById(idConfirmacao)?.value;
        
        limparAviso(avisoId);
        if (!confirmacao) { this._mostrarErro("Confirme sua senha.", avisoId); return false; }
        if (senha !== confirmacao) { this._mostrarErro("As senhas não conferem!", avisoId); return false; }
        return true;
    }

    _mostrarErro(msg, idAviso, inputElement) {
        avisoFalas(msg, idAviso);
        if (inputElement) inputElement.classList.add('input-erro');
    }

    _validarDataNascimento(dataString, avisoId, input) {
        if (!dataString) { this._mostrarErro("Selecione uma data.", avisoId, input); return false; }
        
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);
        const parts = dataString.split('-');
        const dataSelecionada = new Date(parts[0], parts[1] - 1, parts[2]);

        if (dataSelecionada > hoje) { this._mostrarErro("Data futura inválida.", avisoId, input); return false; }
        
        const dataMinima = new Date(hoje);
        dataMinima.setFullYear(hoje.getFullYear() - 18);
        if (dataSelecionada > dataMinima) { this._mostrarErro("Você deve ter +18 anos.", avisoId, input); return false; }
        
        return true;
    }
}