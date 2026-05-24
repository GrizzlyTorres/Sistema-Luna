// frontend/js/auth.js

// Verifica se o usuário tem um token na sessão
const token = sessionStorage.getItem("luna_auth_token");

// Se não tiver token, chuta o usuário para a tela de login
if (!token) {
    window.location.href = "login.html";
}

// Função para o botão "Sair" do menu
function fazerLogout(event) {
    event.preventDefault(); // Evita o comportamento padrão do link
    sessionStorage.removeItem("luna_auth_token"); // Apaga a "chave"
    window.location.href = "login.html"; // Manda pro login
}

// Atribui a função de logout aos botões com a classe btn-sair, caso existam na página
document.addEventListener("DOMContentLoaded", () => {
    const btnSair = document.querySelector(".btn-sair");
    if (btnSair) {
        btnSair.addEventListener("click", fazerLogout);
    }
});