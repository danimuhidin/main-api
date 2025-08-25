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
        Schema::create('motorinci_available_colors', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('motor_id')->constrained('motorinci_motors')->onDelete('cascade');
            $table->foreignId('color_id')->constrained('motorinci_colors')->onDelete('cascade');

            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorinci_available_colors');
    }
};
