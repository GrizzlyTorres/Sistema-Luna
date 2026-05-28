<?php
require_once 'verifica_auth.php';

$dados = json_decode(file_get_contents("php://input"));

if (!$dados || empty($dados->paciente) || empty($dados->data_consulta) || empty($dados->horario)) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "mensagem" => "Paciente, Data e Horário são obrigatórios."]);
    exit;
}

try {
    $sql = "INSERT INTO agendamentos (paciente_info, data_consulta, horario, tipo_atendimento, informacoes) 
            VALUES (:paciente, :data, :horario, :tipo, :info)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':paciente' => $dados->paciente,
        ':data' => $dados->data_consulta,
        ':horario' => $dados->horario,
        ':tipo' => $dados->tipo_atendimento ?? null,
        ':info' => $dados->informacoes ?? null
    ]);
    
    http_response_code(201);
    echo json_encode(["sucesso" => true, "mensagem" => "Sessão agendada com sucesso!"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao agendar sessão."]);
}
?>