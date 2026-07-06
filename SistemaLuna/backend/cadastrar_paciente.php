<?php
require_once 'verifica_auth.php'; 

$dados = json_decode(file_get_contents("php://input"));

if (!$dados || empty($dados->nome) || empty($dados->cpf) || empty($dados->nascimento)) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "mensagem" => "Nome, Data de Nascimento e CPF são obrigatórios."]);
    exit;
}

try {
    $sql = "INSERT INTO pacientes (nome, data_nascimento, cpf, telefone, email, endereco, naturalidade, escolaridade, estado_civil, religiao, indicacao, observacoes, genero) 
            VALUES (:nome, :nascimento, :cpf, :telefone, :email, :endereco, :naturalidade, :escolaridade, :estado_civil, :religiao, :indicacao, :observacoes, :genero)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $dados->nome,
        ':nascimento' => $dados->nascimento,
        ':cpf' => $dados->cpf,
        ':telefone' => $dados->telefone ?? '',
        ':email' => $dados->email ?? null,
        ':endereco' => $dados->endereco ?? null,
        ':naturalidade' => $dados->naturalidade ?? null,
        ':escolaridade' => $dados->escolaridade ?? null,
        ':estado_civil' => $dados->estado_civil ?? null,
        ':religiao' => $dados->religiao ?? null,
        ':indicacao' => $dados->indicacao ?? null,
        ':observacoes' => $dados->observacoes ?? null
        ':genero' => $dados->genero ?? null
    ]);
    
    http_response_code(201);
    echo json_encode(["sucesso" => true, "mensagem" => "Paciente cadastrado com sucesso!"]);
} catch (PDOException $e) {
    http_response_code(500);
    if ($e->errorInfo[1] == 1062) {
        echo json_encode(["sucesso" => false, "mensagem" => "Este CPF já está cadastrado."]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao salvar no banco de dados."]);
    }
}
?>