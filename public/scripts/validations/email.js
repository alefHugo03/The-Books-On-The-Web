import {avisoFalas, limparAviso, etapa} from "/public/src/validations/utilits.js"

export const validarEmail = () => {
    const inputEmail = document.getElementById("email");
    const email = inputEmail.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // CORRIGIDO: Adicionado 'const'
    const nomeFalas = ["O campo email não pode estar vazio.", "Por favor, insira um email válido."];
    const ID_AVISO = etapa[1];

    if (email === "") return avisoFalas(nomeFalas[0], ID_AVISO); 
    if (!emailRegex.test(email)) return avisoFalas(nomeFalas[1], ID_AVISO);

    limparAviso(ID_AVISO); // ADICIONADO
    return email;
};
