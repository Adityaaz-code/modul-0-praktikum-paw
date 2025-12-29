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
        Schema::create('detail_pencatatan_p_os', function (Blueprint $table) {
            $table->id();
            // Relasi ke Pencatatan PO dan Bahan Baku
            $table->foreignId('pencatatan_po_id')->constrained('pencatatan_p_os')->onDelete('cascade');
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            $table->integer('jumlah_beli');
            $table->decimal('harga_satuan', 10, 2);
            $table->timestamps();
            $table->unique(['pencatatan_po_id', 'bahan_baku_id']); // Mencegah duplikasi item dalam satu PO
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pencatatan_p_o_s');
    }
};
