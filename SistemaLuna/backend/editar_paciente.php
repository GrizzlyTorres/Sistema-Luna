<?php
require_once 'verifica_auth.php';

$dados = json_decode(file_get_contents("php://input"));

if (!$dados || empty($dados->id) || empty($dados->nome) || empty($dados->cpf) || empty($dados->nascimento)) {
    http_response_code(400);
    echo json_encode(["sucesso" => false, "mensagem" => "Dados obrigatórios em falta."]);
    exit;
}

try {
    $sql = "UPDATE pacientes SET 
                nome = :nome, 
                data_nascimento = :nascimento, 
                cpf = :cpf, 
                telefone = :telefone, 
                email = :email, 
                endereco = :endereco, 
                naturalidade = :naturalidade, 
                escolaridade = :escolaridade, 
                estado_civil = :estado_civil, 
                religiao = :religiao, 
                indicacao = :indicacao, 
                observacoes = :observacoes,
                genero = :genero,
                sexo = :sexo
            WHERE id = :id";
    
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
        ':observacoes' => $dados->observacoes ?? null,
        ':genero' => $dados->genero ?? null,
        ':sexo' => $dados->sexo ?? null,
        ':id' => $dados->id
    ]);
    
    http_response_code(200);
    echo json_encode(["sucesso" => true, "mensagem" => "Dados atualizados com sucesso!"]);
} catch (PDOException $e) {
    http_response_code(500);
    if ($e->errorInfo[1] == 1062) {
        echo json_encode(["sucesso" => false, "mensagem" => "Este CPF já está em uso por outro paciente."]);
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Erro ao salvar as edições."]);
    }
}
?>