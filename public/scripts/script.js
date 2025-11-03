document.addEventListener("DOMContentLoaded", function() {
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
                    element.classList.add('loaded'); // Para o seu fade-in
                } else {
                    console.warn(`O seletor placeholder "${selector}" não foi encontrado no HTML.`);
                }
            })
            .catch(error => {
                console.error('Falha no loadComponent:', error);
                const errorElement = document.querySelector(selector);
                if (errorElement) {
                    errorElement.innerHTML = `<p style="color:red; text-align:center;">Erro ao carregar componente.</p>`;
                    errorElement.classList.add('loaded');
                }
                throw error;
            });
    }


    const basePath = "/ProjetoM2/The-Books-On-The-Web/public/templates/corpos/";

    loadComponent("#header-placeholder", basePath + "header.html")
        .then(() => {
            console.log("Header carregado. Procurando o formulário...");

            const form = document.getElementById('pesquisar');
            const inputPesquisa = document.getElementById('campoPesquisa');

            if (form && inputPesquisa) {
                console.log("Formulário encontrado. Adicionando 'submit' listener.");
                
                form.addEventListener('submit', event => {
                    event.preventDefault(); 
                    
                    const valorPesquisa = inputPesquisa.value;
                    alert('Você pesquisou por: ' + valorPesquisa);
                });
            } else {
                console.error("ERRO: Não foi possível encontrar #pesquisar ou #campoPesquisa no header.");
            }
        })
        .catch(error => {
            console.error("Não foi possível carregar o header. O formulário de pesquisa não será ativado.");
        });

    loadComponent("#footer-placeholder", basePath + "footer.html");
});