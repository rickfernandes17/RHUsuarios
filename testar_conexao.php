<?php
// Script temporário para testar conexão com PostfixAdmin remoto
// Executar: php artisan tinker --execute="require 'testar_conexao.php';"

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo PHP_EOL . "=== Teste de Conexão PostfixAdmin ===" . PHP_EOL;

try {
    $pdo = DB::connection('mysql_postfix')->getPdo();
    echo "✔ CONEXÃO OK! PostfixAdmin em localhost está acessível." . PHP_EOL;

    // Verificar tabelas disponíveis
    $tables = DB::connection('mysql_postfix')->select("SHOW TABLES");
    echo "✔ Tabelas encontradas no banco postfixadmin: " . count($tables) . PHP_EOL;
    foreach ($tables as $t) {
        $t = (array) $t;
        echo "   - " . reset($t) . PHP_EOL;
    }
} catch (Exception $e) {
    echo "✘ ERRO ao conectar: " . $e->getMessage() . PHP_EOL;
    echo PHP_EOL . "Verifique:" . PHP_EOL;
    echo "  1. Se o MySQL no servidor 192.168.0.111 aceita conexões remotas (bind-address)" . PHP_EOL;
    echo "  2. Se o usuário 'rh_postfix_user' foi criado com acesso remoto" . PHP_EOL;
    echo "  3. Se a senha em DB_POSTFIX_PASSWORD no .env está correta" . PHP_EOL;
    echo "  4. Se o firewall do servidor libera a porta 3306" . PHP_EOL;
}
