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
        Schema::create('motorinci_motor_specifications', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('motor_id')->constrained('motorinci_motors')->onDelete('cascade');
            $table->foreignId('specification_item_id')->constrained('motorinci_specification_items')->onDelete('cascade');

            $table->string('value');
            $table->timestamps();

            // Menambahkan unique constraint untuk mencegah duplikasi data
            // Satu motor hanya bisa memiliki satu nilai untuk satu jenis spesifikasi
            $table->unique(['motor_id', 'specification_item_id'], 'motor_spec_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorinci_motor_specifications');
    }
};
