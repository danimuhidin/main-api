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
        Schema::create('motorinci_motor_images', function (Blueprint $table) {
            $table->id();

            // Foreign key
            $table->foreignId('motor_id')->constrained('motorinci_motors')->onDelete('cascade');

            $table->string('image');
            $table->text('desc')->nullable();
            $table->unsignedInteger('order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorinci_motor_images');
    }
};
