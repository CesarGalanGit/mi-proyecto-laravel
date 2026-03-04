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
        Schema::table('cars', function (Blueprint $table) {
            $table->softDeletes();
            $table->index(['brand', 'model']);
            $table->index('status');
            $table->index('featured');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
            $table->index('status');
            $table->index('order_number');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('car_id');
            $table->index('order_id');
        });

        Schema::table('car_referral_clicks', function (Blueprint $table) {
            $table->index('car_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['brand', 'model']);
            $table->dropIndex(['status']);
            $table->dropIndex(['featured']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['status']);
            $table->dropIndex(['order_number']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['car_id']);
            $table->dropIndex(['order_id']);
        });

        Schema::table('car_referral_clicks', function (Blueprint $table) {
            $table->dropIndex(['car_id']);
            $table->dropIndex(['user_id']);
        });
    }
};
