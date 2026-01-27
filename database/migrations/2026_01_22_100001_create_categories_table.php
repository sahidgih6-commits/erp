<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable(); // Emoji or icon class
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['business_id', 'is_active']);
        });

        // Add category_id to products table
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('business_id')->constrained()->onDelete('set null');
            $table->string('barcode')->nullable()->after('sku')->index();
            $table->string('image')->nullable()->after('barcode');
            $table->decimal('min_stock_level', 10, 2)->default(10)->after('current_stock');
            $table->string('unit')->default('pcs')->after('min_stock_level'); // pcs, kg, ltr, etc
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'barcode', 'image', 'min_stock_level', 'unit']);
        });
        
        Schema::dropIfExists('categories');
    }
};
