<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$dados = json_decode(file_get_contents("php://input"));

if (!isset($dados->email) || empty($dados->email) || !isset($dados->senha) || empty($dados->senha)) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "mensagem" => "E-mail e senha são obrigatórios."]);
    exit;
}

$email = trim($dados->email);
$senha = trim($dados->senha);

// 1. Configurações do Banco de Dados (Mude de acordo com seu ambiente local)
$host = "localhost";
$db_name = "clinica_luna";
$username = "root"; // Padrão do XAMPP
$password = "";     // Padrão do XAMPP geralmente é vazio

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db_name};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Busca o usuário pelo e-mail
    $stmt = $pdo->prepare("SELECT id, nome, senha_hash FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verifica a senha criptografada
    if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
        http_response_code(200);
        echo json_encode([
            "sucesso" => true,
            "token" => bin2hex(random_bytes(16)), // Gera um token de sessão
            "nome_usuario" => $usuario['nome'],
            "mensagem" => "Login aprovado"
        ]);
        exit;
    } else {
        // Falha no login
        http_response_code(401);
        echo json_encode(["sucesso" => false, "mensagem" => "E-mail ou senha incorretos."]);
        exit;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro de conexão com o banco de dados."]);
    exit;
}
?>