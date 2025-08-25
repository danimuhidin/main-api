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
        Schema::create('motorinci_motor_feature', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('motor_id')->constrained('motorinci_motors')->onDelete('cascade');
            $table->foreignId('feature_item_id')->constrained('motorinci_feature_items')->onDelete('cascade');

            $table->timestamps();

            // Menambahkan unique constraint untuk mencegah duplikasi data
            $table->unique(['motor_id', 'feature_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorinci_motor_feature');
    }
};
