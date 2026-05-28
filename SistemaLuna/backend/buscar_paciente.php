<?php
require_once 'verifica_auth.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "mensagem" => "ID não fornecido."]);
    exit;
}

try {
    $sql = "SELECT * FROM pacientes WHERE id = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $_GET['id']]);
    $paciente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paciente) {
        http_response_code(200);
        echo json_encode(["sucesso" => true, "paciente" => $paciente]);
    } else {
        http_response_code(404);
        echo json_encode(["sucesso" => false, "mensagem" => "Paciente não encontrado."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao buscar paciente."]);
}
?>