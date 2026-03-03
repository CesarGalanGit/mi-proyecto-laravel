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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('mileage');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->string('color');
            $table->string('city');
            $table->boolean('featured')->default(false);
            $table->string('status')->default('available');
            $table->string('thumbnail_url')->nullable();
            $table->json('gallery')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['status', 'featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
