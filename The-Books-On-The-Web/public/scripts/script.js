document.addEventListener("DOMContentLoaded", function() {

    function ativarFormularioBusca() {
        const form = document.getElementById('pesquisar');
        const inputPesquisa = document.getElementById('campoPesquisa');

        if (form && inputPesquisa && !form.dataset.listenerAtivo) {
            console.log("Formulário de busca ATIVADO.");
            
            form.dataset.listenerAtivo = 'true';
            form.addEventListener('submit', event => {
                const valorPesquisa = inputPesquisa.value;
            });
        }
    }
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

    ativarFormularioBusca();


    const basePath = "/ProjetoM2/The-Books-On-The-Web/public/templates/corpos/";
    loadComponent("#header-placeholder", basePath + "header.php");
    loadComponent("#footer-placeholder", basePath + "footer.html");

});