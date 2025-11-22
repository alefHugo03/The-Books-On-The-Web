import {avisoFalas, limparAviso, etapa} from "./utilits.js"


export const validarSenha = (senhaValue) => {
    const inputSenha = document.getElementById(senhaValue);
    const senha = inputSenha.value;
    const nomeFalas = ["O campo senha não pode estar vazio.", "A senha deve ter pelo menos 6 caracteres."];
    const ID_AVISO = etapa[3];

    if (senha === "") return avisoFalas(nomeFalas[0], ID_AVISO);
    if (senha.length < 6) return avisoFalas(nomeFalas[1], ID_AVISO);
    
    limparAviso(ID_AVISO);
    return senha;
};
export const validarConfirmarSenha = (senha, senhaDois) => {
    const inputConfirmarSenha = document.getElementById(senhaDois);
    const confirmarSenha = inputConfirmarSenha.value;
    const nomeFalas = ["O campo não pode estar vazio.", "As senhas não conferem!"];
    const ID_AVISO = etapa[4];

    if (confirmarSenha === "") return avisoFalas(nomeFalas[0], ID_AVISO);
    if (senha !== confirmarSenha) return avisoFalas(nomeFalas[1], ID_AVISO);

    limparAviso(ID_AVISO);
    return true;
};