export const etapa = [
    "avisoNome",       
    "avisoEmail",      
    "avisoData",       
    "avisoSenha",      
    "avisoSenhaDois", 
    "avisoCpf",        
    "avisoTipo",      
    "avisoTitulo",     
    "avisoDescricao",  
    "avisoDataPubli", 
    "avisoCategoria",  
    "avisoPdf"        
];
export const avisoFalas = (fala , etapa) => {
    const avisoElement = document.getElementById(etapa);

    avisoElement.innerHTML = fala;
    avisoElement.classList.add('aviso-ativo');
    setTimeout(() => { 
        avisoElement.classList.remove('aviso-ativo');
        avisoElement.innerHTML = ""; 
    }, 4000);
    return;
};

export const limparAviso = (etapa) => {
    const avisoElement = document.getElementById(etapa);
    if (avisoElement) {
        avisoElement.innerHTML = ""; 
        avisoElement.classList.remove('aviso-ativo');
    }
};

