import { avisoFalas, limparAviso, etapa } from "./utilits.js";

// --- Validação do Título ---
export const validarTitulo = (idInput) => {
    const input = document.getElementById(idInput);
    const valor = input.value.trim();
    const ID_AVISO = etapa[7]; // "avisoTitulo"
    const falas = ["O título do livro é obrigatório.", "O título deve ter pelo menos 2 caracteres."];

    if (valor === "") return avisoFalas(falas[0], ID_AVISO);
    if (valor.length < 2) return avisoFalas(falas[1], ID_AVISO);

    limparAviso(ID_AVISO);
    return valor;
};

// --- Validação da Descrição ---
export const validarDescricao = (idInput) => {
    const input = document.getElementById(idInput);
    const valor = input.value.trim();
    const ID_AVISO = etapa[8]; // "avisoDescricao"
    const falas = ["A descrição é obrigatória.", "A descrição deve ter pelo menos 10 caracteres."];

    if (valor === "") return avisoFalas(falas[0], ID_AVISO);
    if (valor.length < 10) return avisoFalas(falas[1], ID_AVISO);

    limparAviso(ID_AVISO);
    return valor;
};

// --- Validação da Data de Publicação ---
export const validarDataPublicacao = (idInput) => {
    const input = document.getElementById(idInput);
    const valor = input.value;
    const ID_AVISO = etapa[9]; // "avisoDataPubli"
    const falas = ["A data de publicação é obrigatória.", "Data inválida."];

    if (!valor) return avisoFalas(falas[0], ID_AVISO);

    // Validação básica se a data é válida
    const data = new Date(valor);
    if (isNaN(data.getTime())) return avisoFalas(falas[1], ID_AVISO);

    limparAviso(ID_AVISO);
    return valor;
};

// --- Validação da Categoria ---
export const validarCategoria = (idInput) => {
    const input = document.getElementById(idInput);
    const valor = input.value;
    const ID_AVISO = etapa[10]; // "avisoCategoria"
    const falas = ["Por favor, selecione uma categoria."];

    if (valor === "" || valor === null) return avisoFalas(falas[0], ID_AVISO);

    limparAviso(ID_AVISO);
    return valor;
};

// --- Validação do PDF ---
// isRequired: true para 'Adicionar', false para 'Editar' (pois o PDF já existe)
export const validarPdf = (idInput, isRequired = true) => {
    const input = document.getElementById(idInput);
    const arquivo = input.files[0];
    const ID_AVISO = etapa[11]; // "avisoPdf"
    const falas = [
        "O arquivo PDF é obrigatório.",
        "O arquivo deve ser um PDF.",
        "O arquivo é muito grande (Máx: 20MB)."
    ];

    // Se não é obrigatório (edição) e está vazio, passa.
    if (!isRequired && input.files.length === 0) {
        limparAviso(ID_AVISO);
        return true; 
    }

    // Se é obrigatório e está vazio
    if (input.files.length === 0) return avisoFalas(falas[0], ID_AVISO);

    // Valida tipo
    if (arquivo.type !== "application/pdf") return avisoFalas(falas[1], ID_AVISO);

    // Valida tamanho (20MB em bytes)
    const maxBytes = 20 * 1024 * 1024;
    if (arquivo.size > maxBytes) return avisoFalas(falas[2], ID_AVISO);

    limparAviso(ID_AVISO);
    return arquivo;
};