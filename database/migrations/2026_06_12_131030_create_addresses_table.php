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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('floor')->nullable();
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('zone_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->text('additional_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
