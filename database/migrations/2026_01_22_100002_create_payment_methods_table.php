<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Cash, Card, bKash, Nagad, etc
            $table->string('name_bn');
            $table->string('type'); // cash, card, mobile_banking, bank_transfer
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['business_id', 'is_active']);
        });

        // Update sales table for multiple payments
        Schema::table('sales', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('sales', 'payment_method')) {
                $table->string('payment_method')->default('cash')->after('total_amount');
            }
            if (!Schema::hasColumn('sales', 'total_price')) {
                $table->decimal('total_price', 10, 2)->default(0)->after('sell_price');
            }
            if (!Schema::hasColumn('sales', 'change_amount')) {
                $table->decimal('change_amount', 10, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('sales', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('change_amount');
            }
            if (!Schema::hasColumn('sales', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('discount_amount');
            }
            if (!Schema::hasColumn('sales', 'note')) {
                $table->text('note')->nullable()->after('discount_type');
            }
            if (!Schema::hasColumn('sales', 'status')) {
                $table->string('status')->default('completed')->after('note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $columns = ['payment_method', 'total_price', 'change_amount', 'discount_amount', 'discount_type', 'note', 'status'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        Schema::dropIfExists('payment_methods');
    }
};
