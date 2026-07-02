
function getAuthHeaders() {
    const token = sessionStorage.getItem("luna_auth_token");
    return {
        "Content-Type": "application/json",
        "Authorization": token ? `Bearer ${token}` : ""
    };
}

document.addEventListener("DOMContentLoaded", async () => {
    const token = sessionStorage.getItem("luna_auth_token");
    
    if (!token && !window.location.pathname.includes("login.html")) {
        window.location.href = "login.html";
        return;
    }

    if (token && window.location.pathname.includes("login.html")) {
        window.location.href = "index.html";
        return;
    }

    if (token) {
        try {
            const resposta = await fetch("../../backend/verifica_auth.php", {
                method: "GET",
                headers: getAuthHeaders()
            });

            if (!resposta.ok) {
                throw new Error("Sessão expirada");
            }
        } catch (erro) {
            console.warn(erro.message);
            sessionStorage.removeItem("luna_auth_token");
            window.location.href = "login.html";
        }
    }

    const btnSair = document.querySelector(".btn-sair");
    if (btnSair) {
        btnSair.addEventListener("click", (e) => {
            e.preventDefault();
            sessionStorage.removeItem("luna_auth_token");
            window.location.href = "login.html";
        });
    }
});
