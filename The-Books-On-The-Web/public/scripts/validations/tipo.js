import {avisoFalas, limparAviso, etapa} from "./utilits.js"

export const validarTipo = (tipoValue) => {
    const inputTipo = document.getElementById(tipoValue);
    const tipo = inputTipo.value;

    const nomeFalas = ["O campo tipo não pode estar vazio."];
    const ID_AVISO = etapa[6];

    if (tipo === "") return avisoFalas(nomeFalas[0], ID_AVISO); 

    limparAviso(ID_AVISO);
    return tipo;
};
