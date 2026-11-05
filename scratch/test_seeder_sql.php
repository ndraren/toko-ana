<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Schema\Grammars\PostgresGrammar as SchemaGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use Illuminate\Database\Query\Processors\PostgresProcessor;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DummyPDO extends PDO {
    private int $id = 0;
    public function __construct() {}
    public function quote($string, $type = PDO::PARAM_STR) {
        return "'" . addslashes($string) . "'";
    }
    public function lastInsertId($name = null) {
        return (string) (++$this->id);
    }
}

$pdo = new DummyPDO();
$conn = new PostgresConnection($pdo, 'postgres', '', ['charset' => 'utf8']);

$schemaGrammar = new SchemaGrammar();
$schemaGrammar->setConnection($conn);
$queryGrammar = new QueryGrammar();
$queryGrammar->setConnection($conn);

$conn->setSchemaGrammar($schemaGrammar);
$conn->setQueryGrammar($queryGrammar);
$conn->setPostProcessor(new PostgresProcessor());

DB::swap($conn);

$seeder = new Database\Seeders\DatabaseSeeder();
$queries = $conn->pretend(function() use ($seeder) {
    try {
        $seeder->run();
    } catch (\Throwable $e) {
        echo "Error in seeder: " . $e->getMessage() . PHP_EOL;
    }
});

echo "Captured " . count($queries) . " insert queries from seeder!" . PHP_EOL;
