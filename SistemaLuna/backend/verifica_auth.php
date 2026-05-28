<?php
header("Content-Type: application/json; charset=UTF-8");

$headers = apache_request_headers();

if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["sucesso" => false, "mensagem" => "Acesso negado. Token não fornecido."]);
    exit; 
}

$tokenEnviado = str_replace("Bearer ", "", $headers['Authorization']);

$host = "localhost";
$db_name = "clinica_luna";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db_name};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE token = :token LIMIT 1");
    $stmt->bindParam(":token", $tokenEnviado);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(401);
        echo json_encode(["sucesso" => false, "mensagem" => "Token inválido ou expirado."]);
        exit;
    }

    
    $usuarioLogado = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro no servidor."]);
    exit;
}
?>
