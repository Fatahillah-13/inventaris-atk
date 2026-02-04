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
        Schema::table('atk_shop_requests', function (Blueprint $table) {
            // Extend status enum for procurement lifecycle
            $table->enum('status', [
                'draft',
                'submitted',
                'waiting_list',
                'ready_to_pickup',
                'done',
            ])->default('draft')->change();

            // Timestamps for Phase 3 flow
            $table->timestamp('waiting_list_at')->nullable()->after('rejection_reason');
            $table->timestamp('finished_at')->nullable()->after('waiting_list_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atk_shop_requests', function (Blueprint $table) {
            $table->dropColumn(['waiting_list_at', 'finished_at']);

            // Revert status enum to Phase 2 values
            $table->enum('status', ['draft', 'submitted', 'waiting_list'])->default('draft')->change();
        });
    }
};
