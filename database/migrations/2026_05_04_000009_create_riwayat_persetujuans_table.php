<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_persetujuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_rapbs_id')->constrained('pengajuan_rapbs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // approver
            $table->enum('status', ['diajukan', 'direvisi', 'disetujui', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_persetujuans');
    }
};
