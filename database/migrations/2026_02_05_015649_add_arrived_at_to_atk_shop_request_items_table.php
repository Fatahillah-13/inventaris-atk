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
        Schema::table('atk_shop_request_items', function (Blueprint $table) {
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('taken_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atk_shop_request_items', function (Blueprint $table) {
            $table->dropColumn(['arrived_at', 'taken_at']);
        });
    }
};
