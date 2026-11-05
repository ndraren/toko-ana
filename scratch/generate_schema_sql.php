<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Schema\Grammars\PostgresGrammar as SchemaGrammar;
use Illuminate\Database\Query\Grammars\PostgresGrammar as QueryGrammar;
use Illuminate\Database\Query\Processors\PostgresProcessor;
use Illuminate\Support\Facades\Schema;

class DummyPDO extends PDO {
    public function __construct() {}
    public function quote($string, $type = PDO::PARAM_STR) {
        return "'" . addslashes($string) . "'";
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

Schema::swap($conn->getSchemaBuilder());

$files = glob(__DIR__ . '/../database/migrations/*.php');
sort($files);

$allSql = [];

foreach ($files as $file) {
    try {
        $migration = require $file;
        if (is_object($migration)) {
            $queries = $conn->pretend(function() use ($migration) {
                $migration->up();
            });
            foreach ($queries as $q) {
                $allSql[] = $q['query'];
            }
        }
    } catch (\Throwable $e) {
        echo "Error in $file: " . $e->getMessage() . PHP_EOL;
    }
}

echo "Total queries collected: " . count($allSql) . PHP_EOL;
file_put_contents(__DIR__ . '/supabase_schema.sql', implode(";\n\n", $allSql) . ";\n");
echo "Saved to scratch/supabase_schema.sql!" . PHP_EOL;
