<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('unit_name');           // "Batang", "Bungkus", "Slop", "Karton"
            $table->integer('conversion_factor');   // berapa base_unit per kemasan (1 untuk satuan dasar)
            $table->integer('sort_order')->default(0); // 0 = satuan dasar, 1+ = kemasan
            $table->timestamps();
        });

        Schema::create('price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_unit_id')->constrained('product_units')->onDelete('cascade');
            $table->decimal('price_retail', 12, 2)->default(0);
            $table->decimal('price_wholesale', 12, 2)->default(0);
            $table->integer('min_wholesale_qty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_tiers');
        Schema::dropIfExists('product_units');
    }
};
