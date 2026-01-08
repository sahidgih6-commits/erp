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
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->string('supplier_name')->nullable()->after('purchase_price');
            $table->string('supplier_phone', 15)->nullable()->after('supplier_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropColumn(['supplier_name', 'supplier_phone']);
        });
    }
};
