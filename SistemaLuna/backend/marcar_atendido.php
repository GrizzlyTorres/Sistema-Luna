<?php
require_once 'verifica_auth.php';

$dados = json_decode(file_get_contents("php://input"));

if (!isset($dados->id) || empty($dados->id)) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "mensagem" => "ID do agendamento não fornecido."]);
    exit;
}

try {
    $sql = "UPDATE agendamentos SET status = 'realizado' WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $dados->id]);

    http_response_code(200);
    echo json_encode(["sucesso" => true, "mensagem" => "Atendimento concluído!"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao atualizar status."]);
}
?>