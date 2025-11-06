import {avisoFalas, limparAviso, etapa} from "/ProjetoM2/The-Books-On-The-Web/public/scripts/validations/utilits.js"

export const validarCpf = () => {
    const inputNome = document.getElementById("cpf");
    const nome = inputNome.value;
    const nomeRegex = /^\d{3}\.\d{3}\.\d{3}\-\d{2}$/;
    const nomeFalas = ["O campo cpf não pode estar vazio.", "O CPF deve conter 11 numeros e - e .", "Escrida do CPF não confere"];
    const ID_AVISO = etapa[5]; // "avisoCpf"

    if (nome === "") return avisoFalas(nomeFalas[0], ID_AVISO); 
    if (nome.length < 14) return avisoFalas(nomeFalas[1] , ID_AVISO);
    if (!nomeRegex.test(nome)) return avisoFalas(nomeFalas[2], ID_AVISO);

    limparAviso(ID_AVISO); 
    return nome;
};