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
        Schema::create('receipt_prints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('pos_transaction_id')->nullable()->constrained('pos_transactions')->onDelete('cascade');
            $table->string('receipt_number')->unique();
            $table->enum('paper_size', ['58mm', '80mm'])->default('80mm');
            $table->string('printer_name')->nullable();
            $table->string('status')->default('pending'); // pending, printing, completed, failed
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index('business_id');
            $table->index('pos_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_prints');
    }
};
