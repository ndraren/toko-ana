<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite: recreate table with new enum value
        DB::statement('CREATE TABLE stock_movements_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER NOT NULL,
            type VARCHAR CHECK(type IN ("in", "out", "damaged", "return", "sale")) DEFAULT "in",
            quantity INTEGER NOT NULL,
            unit VARCHAR DEFAULT "Unit",
            source_category VARCHAR DEFAULT "Supplier",
            notes VARCHAR,
            user_name VARCHAR DEFAULT "Budi Santoso",
            created_at TIMESTAMP,
            updated_at TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )');

        DB::statement('INSERT INTO stock_movements_new SELECT * FROM stock_movements');
        DB::statement('DROP TABLE stock_movements');
        DB::statement('ALTER TABLE stock_movements_new RENAME TO stock_movements');
    }

    public function down(): void
    {
        DB::statement('CREATE TABLE stock_movements_old (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER NOT NULL,
            type VARCHAR CHECK(type IN ("in", "out", "damaged", "return")) DEFAULT "in",
            quantity INTEGER NOT NULL,
            unit VARCHAR DEFAULT "Unit",
            source_category VARCHAR DEFAULT "Supplier",
            notes VARCHAR,
            user_name VARCHAR DEFAULT "Budi Santoso",
            created_at TIMESTAMP,
            updated_at TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )');

        DB::statement('INSERT INTO stock_movements_old SELECT * FROM stock_movements WHERE type != "sale"');
        DB::statement('DROP TABLE stock_movements');
        DB::statement('ALTER TABLE stock_movements_old RENAME TO stock_movements');
    }
};