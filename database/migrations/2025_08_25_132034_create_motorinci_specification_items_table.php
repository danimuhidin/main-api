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
        Schema::create('motorinci_specification_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specification_group_id')->constrained('motorinci_specification_groups')->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('unit')->nullable();
            $table->text('desc')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorinci_specification_items');
    }
};
