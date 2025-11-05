// Pega o formulário pelo ID
const formLogin = document.getElementById("form-login");
const etapa = ["avisoEmail", "avisoSenha"];

formLogin.addEventListener('submit', processarDadosLogin);

function processarDadosLogin(event) {
    event.preventDefault(); 
    console.log("Formulário interceptado pelo JS.");

    const email = validarEmail();
    const senha = validarSenha();
    
    if (!email || !senha) return;

    const dados = new FormData(formLogin); 

    fetch('/ProjetoM2/The-Books-On-The-Web/public/src/login/processar_login.php', {
        method: 'POST',
        body: dados 
    })
    .then(response => response.json()) 
    .then(data => {
        console.log("Resposta do servidor:", data);
        
        if (data.sucesso) {
            window.location.href = data.redirect_url;
        } else {
            avisoFalas(data.mensagem, etapa[0]); 
        }
    })
    .catch(error => {
        console.error("Erro no fetch:", error);
        avisoFalas("Erro de conexão. Tente mais tarde.", etapa[0]);
    });
}

const validarEmail = () => {
    const entradaEmail = document.getElementById("emailEntrar");
    const email = entradaEmail.value;
    const avisoElement =  document.getElementById("avisoEmail");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const nomeFalas = ["Digite um email válido", "O campo não pode estar vazio."]

    if (email === "") return avisoFalas(nomeFalas[1], etapa[0]);
    if (!emailRegex.test(email)) return avisoFalas(nomeFalas[0], etapa[0]);
        
    avisoElement.innerHTML = ""; 
    avisoElement.classList.remove('aviso-ativo');
    return email;
};

const validarSenha = () => {
    const entradaSenha = document.getElementById("senhaEntrar");
    const senha = entradaSenha.value; 
    const avisoElement = document.getElementById("avisoSenha");
    const nomeFalas = ["Digite uma senha válida", "O campo não pode estar vazio."]

    if (senha === "") return avisoFalas(nomeFalas[1], etapa[1]);
    if (senha.length < 6) return avisoFalas(nomeFalas[0], etapa[1]); // Senha fraca (só um exemplo)

    avisoElement.innerHTML = ""; 
    avisoElement.classList.remove('aviso-ativo');
    
    return senha;
};

const avisoFalas = (fala , etapa) => {
    const avisoElement = document.getElementById(etapa);

    avisoElement.innerHTML = fala;
    avisoElement.classList.add('aviso-ativo');
    setTimeout(() => { 
        avisoElement.classList.remove('aviso-ativo');
        avisoElement.innerHTML = ""; 
    }, 4000);
    return; // Retorna undefined (falsy)
};