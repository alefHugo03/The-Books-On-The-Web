<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>The book's on the web</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <header class="main-header">
    <div class="container">
      <h1>The book's on the web</h1>
      <nav>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="#categorias">Categorias</a></li>
          <li><a href="#sobre">Sobre</a></li>
          <li><a href="#contato">Contato</a></li>
          <?php
          session_start();
          if (isset($_SESSION['user_id'])) {
              if ($_SESSION['user_tipo'] === 'admin') {
                  echo '<li><a href="admin/index.php">Painel Admin</a></li>';
              }
              echo '<li><a href="perfil.php">Meu Perfil</a></li>';
              echo '<li>Olá, ' . htmlspecialchars($_SESSION['user_nome']) . '</li>';
          } else {
              echo '<li><a href="login.php">Entrar na conta</a></li>';
          }
          ?>
        </ul>
      </nav>
    </div>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert success">
            <?php 
            echo htmlspecialchars($_SESSION['message']); 
            unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>
  </header>

  <main class="container">
    <section class="books-grid">
      <?php
      // inclui conexão e funções de livros
      require_once __DIR__ . '/config/db.php';
      require_once __DIR__ . '/api/books.php';

      $books = [];
      if (function_exists('getAllBooks')) {
        $books = getAllBooks();
      }

      if (empty($books)) {
        echo '<p>Não há livros disponíveis no momento.</p>';
      } else {
        foreach ($books as $book) {
          $img = !empty($book['imagem']) ? $book['imagem'] : 'https://via.placeholder.com/300x400?text=Capa';
          $titulo = isset($book['titulo']) ? $book['titulo'] : 'Título indisponível';
          $autor = isset($book['autor']) ? $book['autor'] : 'Autor desconhecido';
          $preco = isset($book['preco']) ? number_format($book['preco'], 2, ',', '.') : '0,00';

          echo '<div class="book-card">';
          echo '<img src="' . htmlspecialchars($img) . '" alt="Capa do livro">';
          echo '<h3>' . htmlspecialchars($titulo) . '</h3>';
          echo '<p class="author">Autor: ' . htmlspecialchars($autor) . '</p>';
          echo '<p class="price">R$ ' . $preco . '</p>';
          echo '<button class="btn-comprar" data-id="' . htmlspecialchars($book['id'] ?? '') . '">Comprar</button>';
          echo '</div>';
        }
      }
      ?>
    </section>
  </main>

  <footer class="main-footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>Sobre Nós</h3>
          <p>Sua livraria online com os melhores preços e títulos.</p>
        </div>
        <div class="footer-section">
          <h3>Contato</h3>
          <p>Email: contato@bookstore.com</p>
          <p>Telefone: (11) 1234-5678</p>
        </div>
        <div class="footer-section">
          <h3>Redes Sociais</h3>
          <p>Facebook | Instagram | Twitter</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 Book Store Online. Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>

  <script src="js/app.js"></script>
</body>
</html>
