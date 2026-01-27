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
        Schema::create('system_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->enum('version', ['basic', 'pro', 'enterprise'])->default('pro');
            $table->boolean('pos_enabled')->default(true); // POS auto-enabled for all businesses
            $table->boolean('barcode_scanner_enabled')->default(true);
            $table->boolean('thermal_printer_enabled')->default(true);
            $table->boolean('cash_drawer_enabled')->default(true);
            $table->timestamp('pos_activated_at')->nullable();
            $table->timestamp('upgraded_at')->nullable();
            $table->string('upgrade_notes')->nullable();
            $table->timestamps();

            $table->unique(['business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_versions');
    }
};
