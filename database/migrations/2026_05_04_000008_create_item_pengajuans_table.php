<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_rapbs_id')->constrained('pengajuan_rapbs')->cascadeOnDelete();
            $table->foreignId('kategori_belanja_id')->constrained('kategori_belanjas')->restrictOnDelete();
            $table->string('nama_item');
            $table->text('deskripsi')->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('satuan', 30); // buah, rim, paket, set, dll.
            $table->decimal('volume', 10, 2);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2); // disimpan (volume * harga_satuan) untuk audit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_pengajuans');
    }
};
