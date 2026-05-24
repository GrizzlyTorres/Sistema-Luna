<?php
// backend/login.php

// 1. Configurações de cabeçalhos
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 2. Captura os dados JSON
$dados = json_decode(file_get_contents("php://input"));

if (!isset($dados->email) || empty($dados->email) || !isset($dados->senha) || empty($dados->senha)) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "mensagem" => "E-mail e palavra-passe são obrigatórios."]);
    exit;
}

$email = trim($dados->email);
$senha = trim($dados->senha);

// 3. Credenciais da Base de Dados
$host = "localhost";
$db_name = "clinica_luna";
$username = "root";
$password = "";

try {
    // Liga à base de dados
    $pdo = new PDO("mysql:host={$host};dbname={$db_name};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. Procura o utilizador
    $stmt = $pdo->prepare("SELECT id, nome, senha_hash FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 5. Verifica a palavra-passe e gera o token
    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        
        $token = bin2hex(random_bytes(16));
        
        $stmtUpdate = $pdo->prepare("UPDATE usuarios SET token = :token WHERE id = :id");
        $stmtUpdate->execute([':token' => $token, ':id' => $usuario['id']]);

        http_response_code(200);
        echo json_encode([
            "sucesso" => true,
            "token" => $token,
            "nome_usuario" => $usuario['nome'],
            "mensagem" => "Login aprovado"
        ]);
        exit;
        
    } else {
        // O Plano B (Se falhar a palavra-passe)
        http_response_code(401);
        echo json_encode(["sucesso" => false, "mensagem" => "E-mail ou palavra-passe incorretos."]);
        exit;
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro de ligação: " . $e->getMessage()]);
    exit;
}
?>