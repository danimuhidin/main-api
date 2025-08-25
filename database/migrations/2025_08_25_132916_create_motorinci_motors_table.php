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
        Schema::create('motorinci_motors', function (Blueprint $table) {
             $table->id();
            $table->string('name');

            // Foreign keys
            $table->foreignId('brand_id')->constrained('motorinci_brands')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('motorinci_categories')->onDelete('cascade');

            $table->year('year_model');
            $table->integer('engine_cc');

            // Menggunakan unsignedBigInteger untuk harga agar bisa menampung nilai besar
            $table->unsignedBigInteger('low_price')->nullable();
            $table->unsignedBigInteger('up_price')->nullable();

            $table->text('desc')->nullable();
            $table->string('brochure_url')->nullable();
            $table->string('sparepart_url')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();

            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorinci_motors');
    }
};
