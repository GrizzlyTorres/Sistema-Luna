<?php
require_once 'verifica_auth.php';

try {
    $sql = "SELECT id as prontuario, nome, data_nascimento, cpf, telefone, data_cadastro FROM pacientes WHERE 1=1";
    $parametros = [];

    if (isset($_GET['nome']) && trim($_GET['nome']) !== "") {
        $sql .= " AND nome LIKE :nome";
        $parametros[':nome'] = '%' . trim($_GET['nome']) . '%';
    }
    
    if (isset($_GET['cpf']) && trim($_GET['cpf']) !== "") {
        $sql .= " AND cpf = :cpf";
        $parametros[':cpf'] = trim($_GET['cpf']);
    }

    if (isset($_GET['prontuario']) && trim($_GET['prontuario']) !== "") {
        $sql .= " AND id = :prontuario";
        $parametros[':prontuario'] = trim($_GET['prontuario']);
    }

    $sql .= " ORDER BY nome ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode(["sucesso" => true, "pacientes" => $pacientes]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao buscar pacientes: " . $e->getMessage()]);
}
?>