<?php
// Configurações de cabeçalhos para aceitar pedidos JSON do frontend
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Captura os dados JSON enviados pelo JavaScript
$dados = json_decode(file_get_contents("php://input"));

// Verifica se os campos foram preenchidos
if (!isset($dados->email) || empty($dados->email) || !isset($dados->senha) || empty($dados->senha)) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "mensagem" => "E-mail e senha são obrigatórios."]);
    exit;
}

$email = trim($dados->email);
$senha = trim($dados->senha);

/* ====================================================================
   EXEMPLO REAL DE LIGAÇÃO À BASE DE DADOS (Descomente para usar)
   ====================================================================
try {
    $pdo = new PDO("mysql:host=localhost;dbname=clinica_luna;charset=utf8mb4", "seu_usuario", "sua_senha");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Usa prepared statements para evitar SQL Injection
    $stmt = $pdo->prepare("SELECT id, nome, senha_hash FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // password_verify verifica a senha contra o hash seguro guardado no banco
    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        http_response_code(200);
        echo json_encode([
            "sucesso" => true,
            "token" => bin2hex(random_bytes(16)), // Gerar um token real ou usar JWT
            "mensagem" => "Login aprovado"
        ]);
        exit;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro no servidor."]);
    exit;
}
==================================================================== */

// ====================================================================
// CÓDIGO DE TESTE TEMPORÁRIO (Apague quando ativar a Base de Dados)
// ====================================================================

// O hash abaixo corresponde à senha "123456" gerada usando password_hash() do PHP
$hashSeguroBancoDeDados = password_hash("123456", PASSWORD_DEFAULT);
$emailValido = "psicologa@luna.com";

if ($email === $emailValido && password_verify($senha, $hashSeguroBancoDeDados)) {
    http_response_code(200);
    echo json_encode([
        "sucesso" => true,
        "token" => "token_valido_gerado_pelo_php_890xyz",
        "mensagem" => "Acesso concedido."
    ]);
} else {
    http_response_code(401); // 401 Unauthorized
    echo json_encode([
        "sucesso" => false, 
        "mensagem" => "E-mail ou senha incorretos."
    ]);
}
?>