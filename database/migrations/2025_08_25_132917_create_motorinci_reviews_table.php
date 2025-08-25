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
        Schema::create('motorinci_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->constrained('motorinci_motors')->onDelete('cascade');
            $table->string('reviewer_name');
            $table->string('reviewer_email');
            $table->tinyInteger('rating')->unsigned(); // Nilai 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorinci_reviews');
    }
};
