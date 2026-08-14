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
        Schema::create('cart_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('cart_variation_id')->nullable()->constrained("cart_variations")->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_options');
    }
};
