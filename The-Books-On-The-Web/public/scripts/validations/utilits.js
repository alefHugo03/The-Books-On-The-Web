// Função para mostrar mensagem de erro (aviso)
export function avisoFalas(mensagem, idElemento) {
    const elemento = document.getElementById(idElemento);
    if (elemento) {
        elemento.innerText = mensagem;
        elemento.style.color = "red";
        elemento.style.display = "block";
        elemento.style.fontSize = "0.9rem";
        elemento.style.marginTop = "5px";
    }
}

// Função para limpar mensagem de erro
export function limparAviso(idElemento) {
    const elemento = document.getElementById(idElemento);
    if (elemento) {
        elemento.innerText = "";
        elemento.style.display = "none";
    }
}

// Array de etapas (se usado em outros scripts)
export const etapa = ["avisoTitulo", "avisoDescricao", "avisoDataPubli", "avisoCategoria", "avisoAutor", "avisoPdf"];
