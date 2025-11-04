document.addEventListener('DOMContentLoaded', function() {
    // Adiciona evento de clique para todos os botões de compra
    const botoesComprar = document.querySelectorAll('.btn-comprar');
    
    botoesComprar.forEach(botao => {
        botao.addEventListener('click', function(e) {
            const bookId = this.getAttribute('data-id');
            adicionarAoCarrinho(bookId);
        });
    });
});

function adicionarAoCarrinho(bookId) {
    // Por enquanto, apenas mostra uma mensagem
    alert('Livro adicionado ao carrinho! ID: ' + bookId);
    
    // Aqui você pode adicionar a lógica para adicionar ao carrinho
    // Por exemplo, fazer uma requisição AJAX para o backend
}
