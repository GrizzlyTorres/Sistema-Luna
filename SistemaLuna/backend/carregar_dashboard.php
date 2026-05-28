<?php
require_once 'verifica_auth.php';

try {
    $sqlHoje = "SELECT id, paciente_info, data_consulta, horario, tipo_atendimento, informacoes 
                FROM agendamentos 
                WHERE data_consulta = CURDATE() AND status = 'agendado' 
                ORDER BY horario ASC";
    $stmtHoje = $pdo->query($sqlHoje);
    $hoje = $stmtHoje->fetchAll(PDO::FETCH_ASSOC);

    $sqlProximos = "SELECT id, paciente_info, data_consulta, horario, tipo_atendimento, informacoes 
                    FROM agendamentos 
                    WHERE data_consulta > CURDATE() AND status = 'agendado' 
                    ORDER BY data_consulta ASC, horario ASC LIMIT 10";
    $stmtProximos = $pdo->query($sqlProximos);
    $proximos = $stmtProximos->fetchAll(PDO::FETCH_ASSOC);

    $sqlRecentes = "SELECT id, paciente_info, data_consulta 
                    FROM agendamentos 
                    WHERE status = 'realizado' 
                    ORDER BY data_consulta DESC, horario DESC LIMIT 5";
    $stmtRecentes = $pdo->query($sqlRecentes);
    $recentes = $stmtRecentes->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode([
        "sucesso" => true,
        "hoje" => $hoje,
        "proximos" => $proximos,
        "recentes" => $recentes
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao carregar dashboard."]);
}
?>