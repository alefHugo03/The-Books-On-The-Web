import { 
    validarTitulo, 
    validarDescricao, 
    validarDataPublicacao, 
    validarCategoria, 
    validarPdf 
} from "../validations/validarLivros.js";

import { etapa, limparAviso } from "../validations/utilits.js";

// ... (seu código existente) ...

// Substitua a lógica de envio do formulário por isso:
const formLivro = document.getElementById('form-cadastro');

if (formLivro) {
    formLivro.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Limpa avisos antigos
        // (Você pode criar um loop específico ou limpar todos)
        etapa.forEach(limparAviso); 

        // Verifica se é edição para saber se o PDF é obrigatório
        const isEditMode = document.getElementById('action').value === 'edit';

        // Executa as validações
        const titulo = validarTitulo('titulo');
        const descricao = validarDescricao('descricao');
        const dataPubli = validarDataPublicacao('data_publi');
        const categoria = validarCategoria('categoria');
        const pdf = validarPdf('pdf_file', !isEditMode); // Obrigatório apenas se NÃO for edição

        // Se algum falhou (retornou undefined/false), para aqui
        if (!titulo || !descricao || !dataPubli || !categoria || !pdf) {
            return;
        }

        // Se chegou aqui, está tudo válido! Envia os dados.
        const dados = new FormData(formLivro);

        fetch('livros.php', {
            method: 'POST',
            body: dados
        })
        .then(response => response.text()) // Usando .text() primeiro para debug caso venha HTML de erro
        .then(texto => {
            // ... sua lógica de sucesso ...
            console.log(texto);
            // Recarregar a página ou atualizar a tabela
            window.location.reload();
        })
        .catch(error => console.error("Erro:", error));
    });
}