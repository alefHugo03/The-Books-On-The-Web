import {avisoFalas, limparAviso, etapa} from "/ProjetoM2/The-Books-On-The-Web/public/scripts/validations/utilits.js"

export const validarNome = (nomeValue) => {
    const inputNome = document.getElementById(nomeValue);
    const nome = inputNome.value;
    const nomeRegex = /^[a-zA-Z\s]+$/;
    const nomeFalas = ["O campo nome não pode estar vazio.", "O nome deve conter mais de 3 letras.", "O nome deve conter apenas letras e espaços."];
    const ID_AVISO = etapa[0]; // "avisoNome"

    if (nome === "") return avisoFalas(nomeFalas[0], ID_AVISO); 
    if (nome.length < 3) return avisoFalas(nomeFalas[1] , ID_AVISO);
    if (!nomeRegex.test(nome)) return avisoFalas(nomeFalas[2], ID_AVISO);

    // Se passou, limpa qualquer aviso antigo e retorna o valor
    limparAviso(ID_AVISO); // CORRIGIDO
    return nome;
};

