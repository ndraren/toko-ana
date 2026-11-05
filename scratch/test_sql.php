<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Database\Schema\Grammars\PostgresGrammar;

$grammar = new PostgresGrammar();
echo get_class($grammar) . PHP_EOL;
