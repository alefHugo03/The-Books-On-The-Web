// scripts/animations/ocultar.js
document.addEventListener("DOMContentLoaded", function() {
    const btnToggle = document.getElementById('btn-toggle-cadastro');
    const formAlvo = document.getElementById('form-cadastro');

    if (btnToggle && formAlvo) {
        btnToggle.addEventListener('click', () => {
            formAlvo.classList.toggle('conteudo-oculto');
            
            // Se for um formulário, reseta ao abrir (opcional)
            if (!formAlvo.classList.contains('conteudo-oculto') && typeof resetForm === 'function') {
                resetForm(); 
            }
        });
    }
});