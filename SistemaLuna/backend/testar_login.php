<?php
// backend/testar_login.php
$host = "localhost";
$db_name = "clinica_luna";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db_name};charset=utf8mb4", $username, $password);
    
    $email = 'psicologa@luna.com'; // O e-mail que estamos a tentar
    $senha_digitada = 'Luna2026';    // A palavra-passe que definimos
    
    echo "<h2>🕵️‍♂️ Diagnóstico de Segurança - Clínica Luna</h2>";
    echo "<hr>";
    
    // 1. Tenta encontrar o utilizador pelo e-mail
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo "<h3 style='color:red;'>❌ FASE 1 FALHOU: E-mail não encontrado!</h3>";
        echo "<p>O PHP procurou por <b>'$email'</b> mas a base de dados disse que não existe.</p>";
        echo "<p><b>Como resolver:</b> Vá ao phpMyAdmin e certifique-se de que não existem espaços em branco no fim do e-mail dentro da tabela.</p>";
    } else {
        echo "<h3 style='color:green;'>✅ FASE 1 SUCESSO: E-mail encontrado!</h3>";
        echo "<p>Utilizador: " . $usuario['nome'] . "</p>";
        
        $hash_db = $usuario['senha_hash'];
        $tamanho_hash = strlen($hash_db);
        
        echo "<p>Hash guardado: <code>" . $hash_db . "</code></p>";
        echo "<p>Tamanho do Hash: <b>" . $tamanho_hash . " caracteres</b>.</p>";
        
        // Alerta de tamanho
        if ($tamanho_hash !== 60) {
            echo "<p style='color:orange;'>⚠️ <b>AVISO:</b> O seu Hash tem $tamanho_hash caracteres, mas devia ter exatamente 60! Provavelmente copiou espaços a mais ou não copiou o código todo.</p>";
        }
        
        // 2. Tenta bater a palavra-passe com o hash
        if (password_verify($senha_digitada, $hash_db)) {
            echo "<h3 style='color:green;'>✅ FASE 2 SUCESSO: A palavra-passe '$senha_digitada' está correta!</h3>";
            echo "<p>O sistema está a funcionar perfeitamente. Se o login pelo HTML continua a falhar, o problema pode estar no JavaScript.</p>";
        } else {
            echo "<h3 style='color:red;'>❌ FASE 2 FALHOU: A palavra-passe não corresponde ao Hash!</h3>";
            echo "<p>O PHP tentou verificar a palavra-passe '$senha_digitada' contra o código guardado, mas eles não encaixam.</p>";
            echo "<p><b>Como resolver:</b> Gere um novo hash, apague o antigo no phpMyAdmin e cole o novo com muito cuidado para não incluir espaços.</p>";
        }
    }
} catch (Exception $e) {
    echo "<h3>❌ Erro de Base de Dados:</h3> " . $e->getMessage();
}
?>