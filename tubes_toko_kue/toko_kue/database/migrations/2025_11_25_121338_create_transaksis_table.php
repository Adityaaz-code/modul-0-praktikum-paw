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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            // Relasi ke Pembeli
            $table->foreignId('pembeli_id')->constrained('pembelis')->onDelete('cascade');
            $table->dateTime('tanggal_transaksi');
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->timestamps();
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
