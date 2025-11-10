import {avisoFalas, limparAviso, etapa} from "./utilits.js"
export const validarData = (dataValue) => {
    const inputNascimento = document.getElementById(dataValue);
    const dataString = inputNascimento.value; 
    const nomeFalas = ["Por favor, selecione sua data de nascimento.", "A data de nascimento não pode ser uma data no futuro.", "Você deve ter pelo menos 18 anos para se cadastrar."];
    const ID_AVISO = etapa[2];

    if (!dataString) return avisoFalas(nomeFalas[0], ID_AVISO);

    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);
    const parts = dataString.split('-');
    const dataSelecionada = new Date(parts[0], parts[1] - 1, parts[2]);

    if (dataSelecionada > hoje) return avisoFalas(nomeFalas[1], ID_AVISO);

    const dataMinima = new Date(hoje);
    dataMinima.setFullYear(hoje.getFullYear() - 18);
    if (dataSelecionada > dataMinima) return avisoFalas(nomeFalas[2], ID_AVISO);
    
    limparAviso(ID_AVISO);
    return dataString; 
};