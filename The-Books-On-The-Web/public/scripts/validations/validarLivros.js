// ARQUIVO: public/scripts/validations/validarLivros.js
import { avisoFalas, limparAviso } from "./utilits.js";

export function validarTitulo(id, idAviso) {
    const input = document.getElementById(id);
    if(!input || !input.value.trim()) {
        avisoFalas("O título é obrigatório.", idAviso);
        return false;
    }
    limparAviso(idAviso);
    return true;
}

export function validarDescricao(id, idAviso) {
    // Descrição opcional, mas se quiser obrigatória, descomente abaixo:
    /*
    const input = document.getElementById(id);
    if(!input || !input.value.trim()) {
        avisoFalas("A descrição é obrigatória.", idAviso);
        return false;
    }
    */
    limparAviso(idAviso);
    return true; 
}

export function validarDataPublicacao(id, idAviso) {
    const input = document.getElementById(id);
    if(!input || !input.value) {
        avisoFalas("Selecione uma data.", idAviso);
        return false;
    }
    limparAviso(idAviso);
    return true;
}

export function validarCategoria(id, idAviso) {
    const input = document.getElementById(id);
    if(!input || !input.value) {
        avisoFalas("Selecione uma opção.", idAviso);
        return false;
    }
    limparAviso(idAviso);
    return true;
}

export function validarPdf(id, idAviso, isRequired = true) {
    const input = document.getElementById(id);
    // Se for obrigatório (cadastro novo) e não tiver arquivo
    if(isRequired && (!input.files || input.files.length === 0)) {
        avisoFalas("O arquivo PDF é obrigatório.", idAviso);
        return false;
    }
    // Se tiver arquivo, verifica se é PDF
    if(input.files && input.files.length > 0) {
        const file = input.files[0];
        if(file.type !== "application/pdf") {
            avisoFalas("Apenas arquivos .pdf são permitidos.", idAviso);
            return false;
        }
    }
    limparAviso(idAviso);
    return true;
}