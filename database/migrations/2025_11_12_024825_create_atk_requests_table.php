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
        Schema::create('atk_requests', function (Blueprint $table) {
            $table->id();
            $table->string('kode_request')->unique(); // REQ-202511110001
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            // user internal yang memproses (optional, public form bisa null)
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->string('peminta');    // nama karyawan
            $table->string('departemen'); // divisi / departemen
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk_requests');
    }
};
