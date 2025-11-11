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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_loan')->unique(); // LOAN-2025-0001
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            // user_id boleh null (form publik)
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->string('peminjam');   // nama karyawan
            $table->string('departemen'); // divisi
            $table->integer('jumlah');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_rencana_kembali')->nullable();
            $table->date('tanggal_kembali')->nullable();
            $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
