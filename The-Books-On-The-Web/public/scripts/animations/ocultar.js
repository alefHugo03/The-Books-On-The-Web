const btnToggle = document.getElementById('btn-toggle-cadastro');

const formAlvo = document.getElementById('form-cadastro');

if (btnToggle && formAlvo) {
    btnToggle.addEventListener('click', () => {
    
    formAlvo.classList.toggle('conteudo-oculto');
  });
  
} else {
  console.error("Erro: Não foi possível encontrar 'btn-toggle-cadastro' ou 'form-cadastro'.");
}
