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
        Schema::create('hardware_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->enum('device_type', ['barcode_scanner', 'thermal_printer', 'cash_drawer']);
            $table->string('device_name');
            $table->string('device_model')->nullable();
            $table->string('device_serial_number')->nullable();
            $table->string('connection_type')->default('usb'); // usb, network, bluetooth
            $table->string('port')->nullable();
            $table->string('ip_address')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_connected')->default(false);
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamp('last_disconnected_at')->nullable();
            $table->json('configuration')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('business_id');
            $table->index('device_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hardware_devices');
    }
};
