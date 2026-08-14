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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('final_price', 10, 2);
            $table->foreignId('order_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
