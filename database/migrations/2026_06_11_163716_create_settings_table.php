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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->json('brand_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('phone')->nullable();
            $table->string('wattsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('facebook')->nullable();
            $table->string('insta')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('ios_app')->nullable();
            $table->string('android_app')->nullable();
            $table->decimal('min_order', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
