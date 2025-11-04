<?php
session_start();
require_once 'config/db.php';

// Se não estiver logado, redireciona para login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM endereco WHERE id_endereco = (SELECT endereco FROM usuarios WHERE id_user = ?)");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$endereco = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $cep = trim($_POST['cep']);
    $rua = trim($_POST['rua']);
    $numero = (int)$_POST['numero'];
    $complemento = trim($_POST['complemento'] ?? '');
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);

    if ($endereco) {
        // Atualiza endereço existente
        $stmt = $conn->prepare("UPDATE endereco SET cep = ?, rua = ?, numero = ?, complemento = ?, bairro = ?, cidade = ?, estado = ? WHERE id_endereco = ?");
        $stmt->bind_param("ssissssi", $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $endereco['id_endereco']);
    } else {
        // Insere novo endereço e atualiza o usuário
        $stmt = $conn->prepare("INSERT INTO endereco (cep, rua, numero, complemento, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissss", $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado);
        
        if ($stmt->execute()) {
            $endereco_id = $stmt->insert_id;
            // Atualiza a referência do endereço no usuário
            $stmt = $conn->prepare("UPDATE usuarios SET endereco = ? WHERE id_user = ?");
            $stmt->bind_param("ii", $endereco_id, $user_id);
        }
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Endereço " . ($endereco ? "atualizado" : "cadastrado") . " com sucesso!";
        header("Location: index.php");
        exit();
    } else {
        $message = "Erro ao " . ($endereco ? "atualizar" : "cadastrar") . " endereço: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completar Endereço - The book's on the web</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>The book's on the web</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="form-container">
            <h2>Complete seu Endereço</h2>
            <?php if (!empty($message)): ?>
                <div class="alert"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" action="completar_endereco.php">
                <div class="form-group">
                    <label for="cep">CEP:</label>
                    <input type="text" id="cep" name="cep" maxlength="9" value="<?php echo isset($endereco['cep']) ? htmlspecialchars($endereco['cep']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="rua">Rua:</label>
                    <input type="text" id="rua" name="rua" value="<?php echo isset($endereco['rua']) ? htmlspecialchars($endereco['rua']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="numero">Número:</label>
                    <input type="number" id="numero" name="numero" value="<?php echo isset($endereco['numero']) ? htmlspecialchars($endereco['numero']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="complemento">Complemento:</label>
                    <input type="text" id="complemento" name="complemento" value="<?php echo isset($endereco['complemento']) ? htmlspecialchars($endereco['complemento']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="bairro">Bairro:</label>
                    <input type="text" id="bairro" name="bairro" value="<?php echo isset($endereco['bairro']) ? htmlspecialchars($endereco['bairro']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="cidade">Cidade:</label>
                    <input type="text" id="cidade" name="cidade" value="<?php echo isset($endereco['cidade']) ? htmlspecialchars($endereco['cidade']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" required>
                        <option value="">Selecione...</option>
                        <?php
                        $estados = array(
                            'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas', 'BA' => 'Bahia',
                            'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo', 'GO' => 'Goiás',
                            'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                            'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná', 'PE' => 'Pernambuco', 'PI' => 'Piauí',
                            'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
                            'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina', 'SP' => 'São Paulo',
                            'SE' => 'Sergipe', 'TO' => 'Tocantins'
                        );
                        foreach ($estados as $uf => $nome) {
                            $selected = (isset($endereco['estado']) && $endereco['estado'] == $nome) ? 'selected' : '';
                            echo "<option value=\"$nome\" $selected>$nome</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Salvar Endereço</button>
            </form>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 Book Store Online. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
    // Adiciona máscara ao CEP
    document.getElementById('cep').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) value = value.substr(0, 8);
        if (value.length > 5) {
            value = value.substr(0, 5) + '-' + value.substr(5);
        }
        e.target.value = value;
    });

    // Busca endereço pelo CEP usando a API ViaCEP
    document.getElementById('cep').addEventListener('blur', function(e) {
        const cep = e.target.value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('rua').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        
                        // Seleciona o estado
                        const estadoSelect = document.getElementById('estado');
                        for (let i = 0; i < estadoSelect.options.length; i++) {
                            if (estadoSelect.options[i].text.includes(data.uf)) {
                                estadoSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }
                })
                .catch(error => console.error('Erro ao buscar CEP:', error));
        }
    });
    </script>
</body>
</html>