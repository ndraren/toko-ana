<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing product data into product_units + price_tiers
        $products = DB::table('products')->get();

        foreach ($products as $product) {
            // 1. Base unit (satuan dasar) — always created
            $baseUnitId = DB::table('product_units')->insertGetId([
                'product_id'        => $product->id,
                'unit_name'         => $product->unit,
                'conversion_factor' => 1,
                'sort_order'        => 0,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            DB::table('price_tiers')->insert([
                'product_unit_id'   => $baseUnitId,
                'price_retail'      => $product->selling_price ?? 0,
                'price_wholesale'   => $product->wholesale_price ?? 0,
                'min_wholesale_qty' => $product->wholesale_min_qty ?: 1,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // 2. Pack/Renceng tier — only if configured
            if (!empty($product->pack_name) && $product->pack_qty > 0) {
                $packUnitId = DB::table('product_units')->insertGetId([
                    'product_id'        => $product->id,
                    'unit_name'         => $product->pack_name,
                    'conversion_factor' => $product->pack_qty,
                    'sort_order'        => 1,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                DB::table('price_tiers')->insert([
                    'product_unit_id'   => $packUnitId,
                    'price_retail'      => $product->pack_price ?? 0,
                    'price_wholesale'   => $product->pack_price ?? 0, // same price for pack
                    'min_wholesale_qty' => 1,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            // 3. Box/Dus tier — only if configured
            if (!empty($product->box_name) && $product->box_qty > 0) {
                $boxUnitId = DB::table('product_units')->insertGetId([
                    'product_id'        => $product->id,
                    'unit_name'         => $product->box_name,
                    'conversion_factor' => $product->box_qty,
                    'sort_order'        => 2,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                DB::table('price_tiers')->insert([
                    'product_unit_id'   => $boxUnitId,
                    'price_retail'      => $product->box_price ?? 0,
                    'price_wholesale'   => $product->box_price ?? 0, // same price for box
                    'min_wholesale_qty' => 1,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }

        // Drop old columns from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'selling_price',
                'wholesale_price',
                'wholesale_min_qty',
                'pack_name',
                'pack_price',
                'pack_qty',
                'box_name',
                'box_price',
                'box_qty',
            ]);
        });
    }

    public function down(): void
    {
        // Re-add old columns
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('selling_price', 12, 2)->default(0)->after('cost_price');
            $table->decimal('wholesale_price', 12, 2)->default(0)->after('selling_price');
            $table->integer('wholesale_min_qty')->default(0)->after('wholesale_price');
            $table->string('pack_name')->nullable()->after('wholesale_min_qty');
            $table->decimal('pack_price', 12, 2)->default(0)->after('pack_name');
            $table->integer('pack_qty')->default(0)->after('pack_price');
            $table->string('box_name')->nullable()->after('pack_qty');
            $table->decimal('box_price', 12, 2)->default(0)->after('box_name');
            $table->integer('box_qty')->default(0)->after('box_price');
        });
    }
};
