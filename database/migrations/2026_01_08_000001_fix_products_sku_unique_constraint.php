<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the existing unique constraint on sku
            $table->dropUnique('products_sku_unique');
            
            // Add composite unique constraint for business_id and sku
            $table->unique(['business_id', 'sku'], 'products_business_sku_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('products_business_sku_unique');
            
            // Restore the original unique constraint
            $table->unique('sku', 'products_sku_unique');
        });
    }
};
