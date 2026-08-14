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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->unsignedInteger('users_count')->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('user_usage_limit')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
