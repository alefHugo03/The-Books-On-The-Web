// O ponto ./ significa "nesta mesma pasta"
import { avisoFalas, limparAviso } from "./utilits.js";

export function validarTitulo(idInput, idAviso) {
    const input = document.getElementById(idInput);
    if (!input || !input.value.trim()) {
        avisoFalas("O título é obrigatório.", idAviso);
        return false;
    }
    limparAviso(idAviso);
    return true;
}

export function validarDescricao(idInput, idAviso) {
    return true; 
}

export function validarDataPublicacao(idInput, idAviso) {
    const input = document.getElementById(idInput);
    if (!input || !input.value) {
        avisoFalas("Selecione uma data.", idAviso);
        return false;
    }
    limparAviso(idAviso);
    return true;
}

export function validarCategoria(idInput, idAviso) {
    const input = document.getElementById(idInput);
    // Verifica se selecionou algo (valor não pode ser vazio)
    if (!input || input.value === "") {
        avisoFalas("Selecione uma opção válida.", idAviso);
        return false;
    }
    limparAviso(idAviso);
    return true;
}

export function validarPdf(idInput, idAviso, obrigatorio = true) {
    const input = document.getElementById(idInput);
    
    if (!obrigatorio && (!input.files || input.files.length === 0)) return true;

    if (!input.files || input.files.length === 0) {
        avisoFalas("O PDF é obrigatório.", idAviso);
        return false;
    }
    
    const file = input.files[0];
    if (file.type !== "application/pdf") {
        avisoFalas("Apenas arquivos PDF são permitidos.", idAviso);
        return false;
    }
    
    limparAviso(idAviso);
    return true;
}