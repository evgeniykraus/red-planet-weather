<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mars_weathers', function (Blueprint $table) {
            $table->id();
            $table->integer('sol')->unique();
            $table->date('earth_date');
            $table->integer('ls');
            $table->integer('mars_month')->index();
            $table->string('season');
            $table->integer('min_temp')->nullable();
            $table->integer('max_temp')->nullable();
            $table->float('pressure')->nullable();
            $table->string('atmo_opacity')->nullable();
            $table->string('sunrise')->nullable();
            $table->string('sunset')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mars_weather');
    }
};
