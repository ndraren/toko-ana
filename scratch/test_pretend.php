<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Schema\Grammars\PostgresGrammar as SchemaGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use Illuminate\Database\Query\Processors\PostgresProcessor;

// Buat mock PDO
class DummyPDO extends PDO {
    public function __construct() {}
}

$pdo = new DummyPDO();
$conn = new PostgresConnection($pdo, 'postgres', '', ['charset' => 'utf8']);
$conn->setSchemaGrammar(new SchemaGrammar());
$conn->setQueryGrammar(new QueryGrammar());
$conn->setPostProcessor(new PostgresProcessor());

// Bind this connection to DB manager
$db = $app->make('db');
$db->extend('mock_pgsql', function() use ($conn) {
    return $conn;
});

echo "Success creating mock PostgresConnection!" . PHP_EOL;
