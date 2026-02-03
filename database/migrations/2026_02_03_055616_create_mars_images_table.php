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
        Schema::create('mars_images', function (Blueprint $table) {
            $table->id();
            $table->integer('mars_month')->index();
            $table->string('image_path');
            $table->text('interpretation');
            $table->json('weather_context')->nullable();
            $table->timestamps();

            $table->index(['mars_month', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mars_images');
    }
};
