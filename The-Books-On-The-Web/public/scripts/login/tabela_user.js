// Importa da pasta classes (sobe um nível ../classes)
import GerenciadorAdmin from "../classes/class.js"; 
import { barraCpf } from "../validations/cpf.js";

document.addEventListener("DOMContentLoaded", function() {
    
    // Inicializa a máscara no campo de CPF do admin
    barraCpf('cpfAdmin');

    const adminPage = new GerenciadorAdmin({
        apiUrl: '/The-Books-On-The-Web/public/api/login/admin/gerenciarUsuarios.php',
        formId: 'form-cadastro',
        tableAtivosId: 'tbody-ativos',
        tableInativosId: 'tbody-inativos',
        tipoSelectId: 'tipo'
    });

    adminPage.init();
});