function alternarDiv() {
    const divDoFormulario = document.getElementById("minhaDiv");
    if (divDoFormulario) {
        divDoFormulario.classList.toggle("conteudo-oculto");
    } else {
        console.error("Erro: Elemento com ID 'minhaDiv' não encontrado.");
    }
}