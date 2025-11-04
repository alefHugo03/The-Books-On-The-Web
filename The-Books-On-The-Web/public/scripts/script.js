document.addEventListener("DOMContentLoaded", function() {

    function ativarFormularioBusca() {
        const form = document.getElementById('pesquisar');
        const inputPesquisa = document.getElementById('campoPesquisa');

        if (form && inputPesquisa && !form.dataset.listenerAtivo) {
            console.log("Formulário de busca ATIVADO.");
            
            // 3. Adiciona a "trava" para não rodar de novo
            form.dataset.listenerAtivo = 'true';

            // 4. Adiciona o listener
            form.addEventListener('submit', event => {
                // event.preventDefault(); // Impede o recarregamento
                const valorPesquisa = inputPesquisa.value;
                // alert('Você pesquisou por: ' + valorPesquisa);
            });
        }
    }

    // ===================================================
    // SUA FUNÇÃO LOADCOMPONENT (MODIFICADA)
    // ===================================================
    function loadComponent(selector, url) {
         return fetch(url) 
           .then(response => {
               if (!response.ok) {
                   throw new Error(`Erro ao buscar ${url}: ${response.statusText}`);
               }
               return response.text();
           })
           .then(data => {
               const element = document.querySelector(selector);
               if (element) {
                   element.innerHTML = data;
                   element.classList.add('loaded');
                   
                   // ****** A MÁGICA ACONTECE AQUI ******
                   // Depois de carregar o componente, tente ativar o form
                   // (Útil se o formulário veio DENTRO do componente)
                   ativarFormularioBusca();

               } else {
                   console.warn(`O seletor placeholder "${selector}" não foi encontrado.`);
               }
           })
           .catch(error => {
               console.error('Falha no loadComponent:', error);
               const errorElement = document.querySelector(selector);
               if (errorElement) {
                   errorElement.innerHTML = `<p style="color:red; text-align:center;">Erro ao carregar componente.</p>`;
                   errorElement.classList.add('loaded');
               }
           });
    }

    // ===================================================
    // EXECUÇÃO PRINCIPAL
    // ===================================================

    // 1. TENTA ATIVAR O FORMULÁRIO IMEDIATAMENTE
    // (Para páginas como index.html que já têm o header)
    ativarFormularioBusca();

    // 2. CHAMA O LOADCOMPONENT
    // (Para páginas que vão carregar o header e footer dinamicamente)
    const basePath = "/ProjetoM2/The-Books-On-The-Web/public/templates/corpos/";
    loadComponent("#header-placeholder", basePath + "header.html");
    loadComponent("#footer-placeholder", basePath + "footer.html");

});