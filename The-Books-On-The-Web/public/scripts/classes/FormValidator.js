import { avisoFalas, limparAviso } from "../validations/utilits.js";

export default class FormValidator {
    constructor() {
        // Regex centralizados para facilitar manutenção
        this.regex = {
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            nome: /^[a-zA-Z\s]+$/,
            // CPF simples para checar formato (apenas números)
            cpf: /^\d{11}$/ 
        };
    }

    /**
     * Método Mestre: Valida um campo com base em regras passadas.
     * @param {string} inputId - ID do input no HTML.
     * @param {string} avisoId - ID da div de aviso no HTML.
     * @param {Array} regras - Lista de regras: ['obrigatorio', 'email', 'min:3', etc]
     * @returns {boolean} - Retorna true se passou, false se falhou.
     */
    validarCampo(inputId, avisoId, regras) {
        const input = document.getElementById(inputId);
        if (!input) return false;

        const valor = input.value.trim();
        
        // Limpa erros antes de começar
        limparAviso(avisoId);

        // Itera sobre cada regra solicitada
        for (let regra of regras) {
            
            // 1. Regra: Obrigatório
            if (regra === 'obrigatorio') {
                if (valor === "") {
                    this._mostrarErro("Este campo não pode estar vazio.", avisoId, input);
                    return false;
                }
            }

            // 2. Regra: Email
            if (regra === 'email') {
                if (!this.regex.email.test(valor)) {
                    this._mostrarErro("Por favor, insira um email válido.", avisoId, input);
                    return false;
                }
            }

            // 3. Regra: Mínimo de caracteres (Ex: 'min:6')
            if (regra.startsWith('min:')) {
                const min = parseInt(regra.split(':')[1]);
                if (valor.length < min) {
                    this._mostrarErro(`O campo deve ter pelo menos ${min} caracteres.`, avisoId, input);
                    return false;
                }
            }

            // 4. Regra: Nome (apenas letras)
            if (regra === 'nome') {
                if (!this.regex.nome.test(valor)) {
                    this._mostrarErro("O nome deve conter apenas letras.", avisoId, input);
                    return false;
                }
            }

            // 5. Regra: Data (Lógica complexa extraída do seu data.js)
            if (regra === 'data') {
                if (!this._validarDataNascimento(valor, avisoId, input)) return false;
            }

             // 6. Regra: CPF (Validação básica de tamanho e formato)
             if (regra === 'cpf') {
                // Remove não dígitos para validar
                const cpfLimpo = valor.replace(/\D/g, '');
                if (cpfLimpo.length !== 11) {
                    this._mostrarErro("O CPF deve conter 11 dígitos.", avisoId, input);
                    return false;
                }
            }
        }

        return true; // Se passou por tudo
    }

    /**
     * Validação específica de comparação de senhas
     */
    validarConfirmacaoSenha(idSenha, idConfirmacao, avisoId) {
        const senha = document.getElementById(idSenha)?.value;
        const confirmacao = document.getElementById(idConfirmacao)?.value;
        
        limparAviso(avisoId);

        if (!confirmacao) {
            this._mostrarErro("Confirme sua senha.", avisoId);
            return false;
        }
        if (senha !== confirmacao) {
            this._mostrarErro("As senhas não conferem!", avisoId);
            return false;
        }
        return true;
    }

    /* --- Métodos Auxiliares (Privados) --- */

    _mostrarErro(msg, idAviso, inputElement) {
        avisoFalas(msg, idAviso);
        // O avisoFalas já adiciona a classe no input anterior, mas se quiser garantir:
        if (inputElement) inputElement.classList.add('input-erro');
    }

    _validarDataNascimento(dataString, avisoId, input) {
        if (!dataString) {
            this._mostrarErro("Selecione uma data.", avisoId, input);
            return false;
        }
        
        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);
        const parts = dataString.split('-');
        const dataSelecionada = new Date(parts[0], parts[1] - 1, parts[2]);

        if (dataSelecionada > hoje) {
            this._mostrarErro("Data não pode ser no futuro.", avisoId, input);
            return false;
        }

        const dataMinima = new Date(hoje);
        dataMinima.setFullYear(hoje.getFullYear() - 18);
        if (dataSelecionada > dataMinima) {
            this._mostrarErro("Você deve ter pelo menos 18 anos.", avisoId, input);
            return false;
        }
        return true;
    }
}