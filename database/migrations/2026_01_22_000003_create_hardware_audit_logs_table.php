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
        Schema::create('hardware_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('hardware_device_id')->nullable()->constrained('hardware_devices')->onDelete('set null');
            $table->string('device_type'); // barcode_scanner, thermal_printer, cash_drawer
            $table->string('action'); // scan, print, open_drawer, connect, disconnect, error
            $table->text('details')->nullable();
            $table->string('status')->default('success'); // success, failed, pending
            $table->string('error_message')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->index('business_id');
            $table->index('user_id');
            $table->index('hardware_device_id');
            $table->index('logged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hardware_audit_logs');
    }
};
