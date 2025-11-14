import {avisoFalas, limparAviso, etapa} from "./utilits.js"


const REGEX_LIMPEZA = /[^\d]/g;
const REGEX_FORMATO_COMPLETO = /^(\d{3})(\d{3})(\d{3})(\d{2})$/;

export const validarCpf = (cpfValue) => {
   
    const inputNome = document.getElementById(cpfValue);
    let nome = inputNome.value;
    const nomeFalas = ["O campo cpf não pode estar vazio.", "O CPF deve conter 11 numeros e - e .", "Escrita do CPF não confere"];
    const ID_AVISO = etapa[5];
    
    //formatação de CPF
    nome = nome.replace(REGEX_LIMPEZA, ''); 
    nome = nome.substring(0, 11); 


    let nomeFormatado = nome;
    const tamanhoLimpo = nome.length;
    
    if (tamanhoLimpo > 9) { 
  
        nomeFormatado = nome.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    } else if (tamanhoLimpo > 6) { 
      
        nomeFormatado = nome.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    } else if (tamanhoLimpo > 3) { 
    
        nomeFormatado = nome.replace(/(\d{3})(\d{1,3})/, '$1.$2');
    }

    nome = nomeFormatado; 
    

    
   
    inputNome.value = nome; 
    

    //avisos retirados devido a formatação resolver parcialmente eles
    if (nome === "") return avisoFalas(nomeFalas[0], ID_AVISO); 
    if (nome.length < 14) return avisoFalas(nomeFalas[1] , ID_AVISO); 
    
    
    limparAviso(ID_AVISO); 
    return nome;
};

export const barraCpf = (id) => {
    const cpfInput = document.getElementById(id);
        if (cpfInput) {
            // Anexa a função importada diretamente ao evento 'input'
            cpfInput.addEventListener('input', () => {
                validarCpf(id);
            });
        };
};