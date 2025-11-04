<?php
// /* Incrementações para o projeto, precisando serem organizados */

// require_once 'conection/conectionBD.php';

// /*Apenas exemplo para aperfeiçoar*/
// $sql = "SELECT * FROM usuarios";
// $result = mysqli_query($conexion, $sql);



// /*Verificar se o email já existe no banco de dados usando PDO*/
// /* OBS: Usar para a validação do login e cadastro de usuário, arrumar apenas para confirmar se a senha está relacionada com email*/

// $emailParaVerificar = _POST['email'];

// $sql = "SELECT 1 FROM usuarios WHERE email = ? LIMIT 1";

// $stmt = $pdo->prepare($sql);
// $stmt->execute([$emailParaVerificar]);

// $usuario = $stmt->fetch();

// if ($usuario) {
//     echo "Opa! Esse email já existe.";
// } else {
//     echo "Email disponível! Pode cadastrar.";
// }

// /* Criando validador de login */

// function verificarLogin(){
//     $email = $_POST['email'];
//     $senha = $_POST['senha'];

//     $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([$email]);

//     $usuario = $stmt->fetch();

//     // Verificação
//     if ($usuario && password_verify($senha, $usuario['senha'])) {

//         echo "Bem-vindo, " . $usuario['nome'] . "!";
        
//         // É aqui que você normalmente iniciaria uma SESSÃO
//         // session_start();
//         // $_SESSION['usuario_id'] = $usuario['id'];
//         // header('Location: painel.php');

//     } else {
//         echo "E-mail ou senha inválidos.";
//     }
// }

// /* Função para protegera pagina */

// function verificaLogin() {
//     // 1. Inicia a sessão (para ler o "crachá" $_SESSION)
//     session_start();

//     // 2. Verifica se o "crachá" NÃO existe
//     // (Eu uso 'usuario_id' porque foi o que definimos no login)
//     if (!isset($_SESSION['usuario_id'])) {
        
//         // 3. Se não existe, manda o usuário para a rua (login.php)
//         header('Location: login.php');
        
//         // 4. Garante que o resto do script não seja executado
//         exit();
//     }
    
//     // 5. Se o 'if' for falso, o usuário está logado. 
//     // A função termina e a página protegida continua carregando.
//     //Login cé considerado valido se login é completado e salva o id do usuario na sessao
// }


// function verificaLogin() {
//     session_start();

//     // Define o tempo limite em segundos (ex: 30 minutos)
//     $tempo_limite = 30 * 60; // 30 minutos * 60 segundos

//     // Pergunta 1: Ele sequer tem o crachá?
//     if (!isset($_SESSION['usuario_id'])) {
//         header('Location: login.php');
//         exit();
//     }

//     // Pergunta 2: O crachá expirou?
//     $agora = time();
//     if ($agora - $_SESSION['ultima_atividade'] > $tempo_limite) {
        
//         // Destrói a sessão (faz o logout)
//         session_unset();
//         session_destroy();

//         // Manda pro login
//         header('Location: login.php?status=expirado');
//         exit();
//     }

//     // Se chegou aqui, tá tudo OK.
//     // Atualiza a hora da última atividade para o momento ATUAL.
//     // Isso "reseta" o cronômetro a cada página carregada.
//     $_SESSION['ultima_atividade'] = $agora;
// }


// if ($usuario && password_verify($senha, $usuario['senha'])) {
//     session_regenerate_id(true); 
//     $_SESSION['usuario_id'] = $usuario['id'];
    
//     // GUARDA A HORA DO LOGIN
//     $_SESSION['ultima_atividade'] = time(); // 'time()' é o N° de segundos desde 1970
    
//     header('Location: painel.php');
//     exit();
// }


// function fecharConexao($conexion) {
//     mysqli_close($conexion);
// }
?>