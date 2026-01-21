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
            // Update status enum to include 'waiting_list'
            $table->enum('status', ['draft', 'submitted', 'waiting_list'])->default('draft')->change();
            
            // Add approval fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('submitted_at');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Add rejection fields
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atk_shop_requests', function (Blueprint $table) {
            // Drop Phase 2 fields
            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
            
            $table->dropForeign(['rejected_by']);
            $table->dropColumn('rejected_by');
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejection_reason');
            
            // Revert status enum to Phase 1 values
            $table->enum('status', ['draft', 'submitted'])->default('draft')->change();
        });
    }
};
