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
        Schema::create('car_referral_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_name')->nullable();
            $table->string('destination_url', 2048);
            $table->string('referrer', 2048)->nullable();
            $table->string('session_id', 120)->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();

            $table->index(['car_id', 'clicked_at']);
            $table->index('source_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_referral_clicks');
    }
};
