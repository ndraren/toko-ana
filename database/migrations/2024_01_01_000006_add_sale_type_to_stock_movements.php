<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL/MariaDB: modify enum column to add 'sale' type
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('in','out','damaged','return','sale') DEFAULT 'in'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('in','out','damaged','return') DEFAULT 'in'");
    }
};