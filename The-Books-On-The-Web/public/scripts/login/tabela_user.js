// ARQUIVO: public/scripts/tabela_user.js
import GerenciadorAdmin from "./classes/class.js"; 

document.addEventListener("DOMContentLoaded", function() {
    
    // Instancia a classe passando as configurações do seu HTML
    const adminPage = new GerenciadorAdmin({
        apiUrl: 'api/login/admin/gerenciarUsuarios.php', // Endpoint da API
        formId: 'form-cadastro',                         // ID do formulário
        tableAtivosId: 'tbody-ativos',                   // ID do corpo da tabela de ativos
        tableInativosId: 'tbody-inativos',               // ID do corpo da tabela de inativos
        tipoSelectId: 'tipo'                             // ID do select de tipo de usuário
    });

    // Inicia o sistema (carrega tabelas, ativa listeners, máscaras, etc.)
    adminPage.init();

});