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

// Collect all migrations table DDL
$files = glob(__DIR__ . '/../database/migrations/*.php');
sort($files);

$sqlStatements = [];

// 1. Create migrations tracking table
$sqlStatements[] = <<<SQL
CREATE TABLE IF NOT EXISTS "migrations" (
    "id" serial PRIMARY KEY,
    "migration" varchar(255) NOT NULL,
    "batch" integer NOT NULL
);
SQL;

// 2. Iterate migration files
foreach ($files as $file) {
    $baseName = basename($file, '.php');
    try {
        $migration = require $file;
        if (is_object($migration)) {
            $queries = $conn->pretend(function() use ($migration) {
                $migration->up();
            });
            foreach ($queries as $q) {
                $sqlStatements[] = $q['query'] . ";";
            }
        }
    } catch (\Throwable $e) {
        // Skip DB::table queries in pretend mode
    }

    $sqlStatements[] = "INSERT INTO \"migrations\" (\"migration\", \"batch\") VALUES ('{$baseName}', 1) ON CONFLICT DO NOTHING;";
}

// 3. Settings table & default values
$sqlStatements[] = <<<SQL
CREATE TABLE IF NOT EXISTS "settings" (
    "id" bigserial NOT NULL PRIMARY KEY,
    "key" varchar(255) NOT NULL UNIQUE,
    "value" text NULL,
    "created_at" timestamp(0) without time zone NULL,
    "updated_at" timestamp(0) without time zone NULL
);

INSERT INTO "settings" ("key", "value", "created_at", "updated_at") VALUES
('store_name', 'Toko Berkah Kelontong', NOW(), NOW()),
('store_status', 'Buka (Operasional Normal)', NOW(), NOW()),
('cashier_name', 'Budi Santoso', NOW(), NOW()),
('shift', 'Kasir Shift 1 (Pagi)', NOW(), NOW()),
('work_shift', 'Kasir Shift 1 (Pagi)', NOW(), NOW()),
('store_address', 'Jl. Contoh Alamat No. 123, Kota', NOW(), NOW()),
('store_phone', '021-12345678', NOW(), NOW())
ON CONFLICT ("key") DO NOTHING;
SQL;

// 4. Default Users (admin & kasir)
$passwordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // 'password'
$sqlStatements[] = <<<SQL
INSERT INTO "users" ("name", "email", "password", "role", "created_at", "updated_at") VALUES
('Administrator Toko', 'admin@toko.com', '{$passwordHash}', 'admin', NOW(), NOW()),
('Kasir 1', 'kasir@toko.com', '{$passwordHash}', 'kasir', NOW(), NOW())
ON CONFLICT ("email") DO NOTHING;
SQL;

$finalSql = implode("\n\n", $sqlStatements);
file_put_contents(__DIR__ . '/supabase_setup.sql', $finalSql);
echo "Successfully generated scratch/supabase_setup.sql! Length: " . strlen($finalSql) . " bytes\n";
