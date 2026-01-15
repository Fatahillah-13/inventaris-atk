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
        Schema::create('atk_shop_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_shop_request_id')->constrained('atk_shop_requests')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('qty')->default(1);
            $table->timestamps();
            
            // Prevent duplicate items in same request
            $table->unique(['atk_shop_request_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk_shop_request_items');
    }
};
