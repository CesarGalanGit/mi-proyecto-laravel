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
            $table->string('source_external_id')->nullable()->after('source_url');
            $table->unsignedInteger('outbound_clicks')->default(0)->after('source_external_id');
            $table->timestamp('last_synced_at')->nullable()->after('outbound_clicks');

            $table->index(['source_name', 'source_external_id']);
            $table->index('outbound_clicks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropIndex(['source_name', 'source_external_id']);
            $table->dropIndex(['outbound_clicks']);
            $table->dropColumn(['source_external_id', 'outbound_clicks', 'last_synced_at']);
        });
    }
};
