<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_rapbs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 50)->unique(); // e.g. RAPBS-2025/2026-001
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->enum('status', ['draft', 'diajukan', 'direvisi', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan')->nullable(); // catatan dari reviewer
            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamps();

            // Satu pengajuan per pegawai per tahun ajaran
            $table->unique(['user_id', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_rapbs');
    }
};
