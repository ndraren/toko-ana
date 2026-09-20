<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Pack tier (Renceng/Pack)
            $table->string('pack_name')->nullable()->after('wholesale_min_qty');
            $table->decimal('pack_price', 12, 2)->default(0)->after('pack_name');
            $table->integer('pack_qty')->default(0)->after('pack_price');
            // Box tier (Dus/Karton)
            $table->string('box_name')->nullable()->after('pack_qty');
            $table->decimal('box_price', 12, 2)->default(0)->after('box_name');
            $table->integer('box_qty')->default(0)->after('box_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['pack_name', 'pack_price', 'pack_qty', 'box_name', 'box_price', 'box_qty']);
        });
    }
};