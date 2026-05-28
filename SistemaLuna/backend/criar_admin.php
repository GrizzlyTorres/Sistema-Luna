<?php

$host = "localhost";
$db_name = "clinica_luna";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db_name};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nome = "Administrador Luna";
    $email = "admin@luna.com";
    $senha_plana = "123456"; 
    $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)");
    
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha_hash' => $senha_hash
    ]);

    echo "<h3>Usuário criado com sucesso!</h3>";
    echo "<p><strong>E-mail:</strong> {$email}</p>";
    echo "<p><strong>Senha:</strong> {$senha_plana}</p>";
    echo "<p>Você já pode ir para a tela de login e testar o acesso.</p>";

} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        echo "O usuário admin@luna.com já existe no banco de dados.";
    } else {
        echo "Erro: " . $e->getMessage();
    }
}
?>