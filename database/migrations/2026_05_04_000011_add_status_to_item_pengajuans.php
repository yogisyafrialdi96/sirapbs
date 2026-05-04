<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_pengajuans', function (Blueprint $table) {
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])
                ->default('pending')
                ->after('total_harga');
            $table->text('catatan_reviewer')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('item_pengajuans', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_reviewer']);
        });
    }
};
