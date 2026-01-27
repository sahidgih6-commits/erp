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
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_number')->unique();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('payment_method'); // cash, card, mobile
            $table->decimal('amount_tendered', 12, 2)->nullable();
            $table->decimal('change', 12, 2)->nullable();
            $table->string('status')->default('completed'); // completed, cancelled, refunded
            $table->boolean('receipt_printed')->default(false);
            $table->boolean('drawer_opened')->default(false);
            $table->json('items')->nullable(); // store cart items as JSON
            $table->text('notes')->nullable();
            $table->timestamp('completed_at');
            $table->timestamps();

            $table->index('business_id');
            $table->index('user_id');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_transactions');
    }
};
